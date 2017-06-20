<?php

class VES_VendorsPaypal_Model_Ipn extends Mage_Paypal_Model_Ipn
{
	protected $_orders;
	
	/**
     * Load and validate order, instantiate proper configuration
     *
     *
     * @return Mage_Sales_Model_Order
     * @throws Exception
     */
    protected function _getOrders()
    {
        if (empty($this->_orders)) {
            // get proper order
            $ids = explode(VES_VendorsPaypal_Model_Standard::SEPARATED_CHAR,$this->_request['invoice']);
			foreach($ids as $id){
				$order = Mage::getModel('sales/order')->loadByIncrementId($id);
				if (!$order->getId()) {
					$this->_debugData['exception'] = sprintf('Wrong order ID: "%s".', $id);
					$this->_debug();
					Mage::app()->getResponse()
						->setHeader('HTTP/1.1','503 Service Unavailable')
						->sendResponse();
					exit;
				}
				// re-initialize config with the method code and store id
				$methodCode = $order->getPayment()->getMethod();
				$this->_config = Mage::getModel('paypal/config', array($methodCode, $order->getStoreId()));
				if (!$this->_config->isMethodActive($methodCode) || !$this->_config->isMethodAvailable()) {
					throw new Exception(sprintf('Method "%s" is not available.', $methodCode));
				}

				$this->_orders[$order->getId()] = $order;
			}
			
			$this->_verifyOrder();
        }
        return $this->_orders;
    }
    /**
     * IPN workflow implementation
     * Everything should be added to order comments. In positive processing cases customer will get email notifications.
     * Admin will be notified on errors.
     */
    protected function _processOrder()
    {
        $this->_orders = null;
        $this->_getOrders();
		
        $this->_info = Mage::getSingleton('paypal/info');
        try {
            // Handle payment_status
            $transactionType = isset($this->_request['txn_type']) ? $this->_request['txn_type'] : null;
            switch ($transactionType) {
                // handle new case created
                case Mage_Paypal_Model_Info::TXN_TYPE_NEW_CASE:
                    $this->_registerDispute();
                    break;

                // handle new adjustment is created
                case Mage_Paypal_Model_Info::TXN_TYPE_ADJUSTMENT:
                    $this->_registerAdjustment();
                    break;

                //handle new transaction created
                default:
                    $this->_registerTransaction();
            }
        } catch (Mage_Core_Exception $e) {
            $comment = $this->_createIpnComment(Mage::helper('paypal')->__('Note: %s', $e->getMessage()), true);
            $comment->save();
            throw $e;
        }
    }
	
	/**
     * Process regular IPN notifications
     */
    protected function _registerTransaction()
    {
		Mage::log('Register transaction',null,'vendorspaypal.log');
        try {
            // Handle payment_status
            $paymentStatus = $this->_filterPaymentStatus($this->_request['payment_status']);
            switch ($paymentStatus) {
                // paid
                case Mage_Paypal_Model_Info::PAYMENTSTATUS_COMPLETED:
					Mage::log('Register payment capture',null,'vendorspaypal.log');
                    $this->_registerPaymentCapture(true);
                    break;

                // the holded payment was denied on paypal side
                case Mage_Paypal_Model_Info::PAYMENTSTATUS_DENIED:
                    $this->_registerPaymentDenial();
                    break;

                // customer attempted to pay via bank account, but failed
                case Mage_Paypal_Model_Info::PAYMENTSTATUS_FAILED:
                    // cancel order
                    $this->_registerPaymentFailure();
                    break;

                // payment was obtained, but money were not captured yet
                case Mage_Paypal_Model_Info::PAYMENTSTATUS_PENDING:
                    $this->_registerPaymentPending();
                    break;

                case Mage_Paypal_Model_Info::PAYMENTSTATUS_PROCESSED:
                    $this->_registerMasspaymentsSuccess();
                    break;

                case Mage_Paypal_Model_Info::PAYMENTSTATUS_REVERSED:// break is intentionally omitted
                case Mage_Paypal_Model_Info::PAYMENTSTATUS_UNREVERSED:
                    $this->_registerPaymentReversal();
                    break;

                case Mage_Paypal_Model_Info::PAYMENTSTATUS_REFUNDED:
                    $this->_registerPaymentRefund();
                    break;

                // authorization expire/void
                case Mage_Paypal_Model_Info::PAYMENTSTATUS_EXPIRED: // break is intentionally omitted
                case Mage_Paypal_Model_Info::PAYMENTSTATUS_VOIDED:
                    $this->_registerPaymentVoid();
                    break;

                default:
                    throw new Exception("Cannot handle payment status '{$paymentStatus}'.");
            }
        } catch (Mage_Core_Exception $e) {
            $comment = $this->_createIpnComment(Mage::helper('paypal')->__('Note: %s', $e->getMessage()), true);
            $comment->save();
            throw $e;
        }
    }
	
	/**
     * Map payment information from IPN to payment object
     * Returns true if there were changes in information
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     */
    protected function _vendorImportPaymentInformation(Mage_Sales_Model_Order $order)
    {
        $payment = $order->getPayment();
        $was = $payment->getAdditionalInformation();

        // collect basic information
        $from = array();
        foreach (array(
            Mage_Paypal_Model_Info::PAYER_ID,
            'payer_email' => Mage_Paypal_Model_Info::PAYER_EMAIL,
            Mage_Paypal_Model_Info::PAYER_STATUS,
            Mage_Paypal_Model_Info::ADDRESS_STATUS,
            Mage_Paypal_Model_Info::PROTECTION_EL,
            Mage_Paypal_Model_Info::PAYMENT_STATUS,
            Mage_Paypal_Model_Info::PENDING_REASON,
        ) as $privateKey => $publicKey) {
            if (is_int($privateKey)) {
                $privateKey = $publicKey;
            }
            $value = $this->getRequestData($privateKey);
            if ($value) {
                $from[$publicKey] = $value;
            }
        }
        if (isset($from['payment_status'])) {
            $from['payment_status'] = $this->_filterPaymentStatus($this->getRequestData('payment_status'));
        }

        // collect fraud filters
        $fraudFilters = array();
        for ($i = 1; $value = $this->getRequestData("fraud_management_pending_filters_{$i}"); $i++) {
            $fraudFilters[] = $value;
        }
        if ($fraudFilters) {
            $from[Mage_Paypal_Model_Info::FRAUD_FILTERS] = $fraudFilters;
        }

        $this->_info->importToPayment($from, $payment);

        /**
         * Detect pending payment, frauds
         * TODO: implement logic in one place
         * @see Mage_Paypal_Model_Pro::importPaymentInfo()
         */
        if ($this->_info->isPaymentReviewRequired($payment)) {
            $payment->setIsTransactionPending(true);
            if ($fraudFilters) {
                $payment->setIsFraudDetected(true);
            }
        }
        if ($this->_info->isPaymentSuccessful($payment)) {
            $payment->setIsTransactionApproved(true);
        } elseif ($this->_info->isPaymentFailed($payment)) {
            $payment->setIsTransactionDenied(true);
        }

        return $was != $payment->getAdditionalInformation();
    }
	
	/**
     * Process completed payment (either full or partial)
     *
     * @param bool $skipFraudDetection
     */
    protected function _registerPaymentCapture($skipFraudDetection = false)
    {
        if ($this->getRequestData('transaction_entity') == 'auth') {
            return;
        }
        $parentTransactionId = $this->getRequestData('parent_txn_id');
		Mage::log('- Payment transaction id: '.$this->getRequestData('txn_id'),null,'vendorspaypal.log');
        $this->_importPaymentInformation();
		$grandTotal = 0;
		foreach($this->_orders as $order){
			$grandTotal += $order->getBaseGrandTotal();
		}
		Mage::log('- Register payment and capture',null,'vendorspaypal.log');
		if($grandTotal != $this->getRequestData('mc_gross')){
			foreach($this->_orders as $order){
				$order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, 
						Mage_Sales_Model_Order::STATUS_FRAUD, 
						Mage::helper('sales')->__('Order is suspended as its capture amount %s is suspected to be fraudulent.',$this->getRequestData('mc_gross'))
				);
			}
			return;
		}
		Mage::log('- validate captured payement',null,'vendorspaypal.log');
		
		foreach($this->_orders as $order){
			$payment = $order->getPayment();
			$payment->setTransactionId($this->getRequestData('txn_id'))
				->setCurrencyCode($this->getRequestData('mc_currency'))
				->setPreparedMessage($this->_createIpnComment(''))
				->setParentTransactionId($parentTransactionId)
				->setShouldCloseParentTransaction('Completed' === $this->getRequestData('auth_status'))
				->setIsTransactionClosed(0)
				->registerCaptureNotification(
					$order->getBaseGrandTotal(),
					$skipFraudDetection && $parentTransactionId
				);
			$order->save();
			Mage::log('- Capture order',null,'vendorspaypal.log');
			// notify customer
			$invoice = $payment->getCreatedInvoice();
			if ($invoice && !$order->getEmailSent()) {
				$order->queueNewOrderEmail()->addStatusHistoryComment(
					Mage::helper('paypal')->__('Notified customer about invoice #%s.', $invoice->getIncrementId())
				)
				->setIsCustomerNotified(true)
				->save();
				Mage::log('- Save Invoice',null,'vendorspaypal.log');
			}
		}
    }
	
	/**
     * Map payment information from IPN to payment object
     * Returns true if there were changes in information
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     */
    protected function _importPaymentInformation()
    {
		foreach($this->_orders as $order){
			$payment = $order->getPayment();
			$was = $payment->getAdditionalInformation();

			// collect basic information
			$from = array();
			foreach (array(
				Mage_Paypal_Model_Info::PAYER_ID,
				'payer_email' => Mage_Paypal_Model_Info::PAYER_EMAIL,
				Mage_Paypal_Model_Info::PAYER_STATUS,
				Mage_Paypal_Model_Info::ADDRESS_STATUS,
				Mage_Paypal_Model_Info::PROTECTION_EL,
				Mage_Paypal_Model_Info::PAYMENT_STATUS,
				Mage_Paypal_Model_Info::PENDING_REASON,
			) as $privateKey => $publicKey) {
				if (is_int($privateKey)) {
					$privateKey = $publicKey;
				}
				$value = $this->getRequestData($privateKey);
				if ($value) {
					$from[$publicKey] = $value;
				}
			}
			if (isset($from['payment_status'])) {
				$from['payment_status'] = $this->_filterPaymentStatus($this->getRequestData('payment_status'));
			}

			// collect fraud filters
			$fraudFilters = array();
			for ($i = 1; $value = $this->getRequestData("fraud_management_pending_filters_{$i}"); $i++) {
				$fraudFilters[] = $value;
			}
			if ($fraudFilters) {
				$from[Mage_Paypal_Model_Info::FRAUD_FILTERS] = $fraudFilters;
			}

			$this->_info->importToPayment($from, $payment);

			/**
			 * Detect pending payment, frauds
			 * TODO: implement logic in one place
			 * @see Mage_Paypal_Model_Pro::importPaymentInfo()
			 */
			if ($this->_info->isPaymentReviewRequired($payment)) {
				$payment->setIsTransactionPending(true);
				if ($fraudFilters) {
					$payment->setIsFraudDetected(true);
				}
			}
			if ($this->_info->isPaymentSuccessful($payment)) {
				$payment->setIsTransactionApproved(true);
			} elseif ($this->_info->isPaymentFailed($payment)) {
				$payment->setIsTransactionDenied(true);
			}
		}
        //return $was != $payment->getAdditionalInformation();
    }
	/**
     * Get ipn data, send verification to PayPal, run corresponding handler
     *
     * @param array $request
     * @param Zend_Http_Client_Adapter_Interface $httpAdapter
     * @throws Exception
     */
    public function processIpnRequest(array $request, Zend_Http_Client_Adapter_Interface $httpAdapter = null)
    {
		Mage::log('process IPN Request',null,'vendorspaypal.log');
        $this->_request   = $request;
        $this->_debugData = array('ipn' => $request);
        ksort($this->_debugData['ipn']);

        try {
            if (isset($this->_request['txn_type']) && 'recurring_payment' == $this->_request['txn_type']) {
                $this->_getRecurringProfile();
                if ($httpAdapter) {
                    $this->_postBack($httpAdapter);
                }
                $this->_processRecurringProfile();
            } else {
                $this->_getOrders();
                if ($httpAdapter) {
                    $this->_postBack($httpAdapter);
                }
				Mage::log('Process order',null,'vendorspaypal.log');
                $this->_processOrder();
            }
        } catch (Exception $e) {
            $this->_debugData['exception'] = $e->getMessage();
            $this->_debug();
            throw $e;
        }
        $this->_debug();
    }
}