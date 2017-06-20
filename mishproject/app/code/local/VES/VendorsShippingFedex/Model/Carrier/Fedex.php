<?php

class VES_VendorsShippingFedex_Model_Carrier_Fedex extends Mage_Usa_Model_Shipping_Carrier_Fedex{
    
    public function groupItemsByVendor(){
        $quotes = array();
        foreach($this->_request->getAllItems() as $item) {
            if($item->getParentItem()) continue;
            if($item->getProduct()->getVendorId()) {
				if($item->getVendorId()){
					$vendorId = $item->getVendorId();
				}else{
					$vendorId = $item->getProduct()->getVendorId();
				}
				$transport = new Varien_Object(array('vendor_id'=>$vendorId,'item'=>$item));
				Mage::dispatchEvent('ves_vendors_checkout_init_vendor_id',array('transport'=>$transport));
				$vendorId = $transport->getVendorId();
                /*Get item by vendor id*/
                if(!isset($quotes[$vendorId])) $quotes[$vendorId] = array();
                $quotes[$vendorId][] = $item;
            } else {
                $quotes['no_vendor'][] = $item;
            }
        }
        return $quotes;
    }
    
    /**
     * Do remote request for and handle errors
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        $this->_result = Mage::getModel('shipping/rate_result');
        // make separate request for Smart Post method
        $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));
        
        $quotes = $this->groupItemsByVendor();
        foreach($quotes as $vendorId=>$items){
            if(!Mage::helper('vendorsconfig')->getVendorConfig('shipping/fedex/active',$vendorId)) continue;
            $vendor = Mage::getModel('vendors/vendor')->load($vendorId);
            $r = $this->_rawRequest;
            
            $vendorCountry  = $vendor->getCountryId();
            $vendorCountry  = Mage::getModel('directory/country')->load($vendorCountry)->getIso2Code();
            $vendorPostcode = $vendor->getPostcode();
            $vendorCity     = $vendor->getCity();
            
            $weight = 0;
            $amount = 0;
            foreach($items as $item){
                $weight+= $item->getWeight()*$item->getQty();
                $amount+= $item->getBaseRowTotal();
            }

            $weight = $this->getTotalNumOfBoxes($weight);
            
            $vendorRequest = new Varien_Object(array(
                'country'   => $vendorCountry,
                'postcode'  => $vendorPostcode,
                'city'      => $vendorCity,
                'weight'    => $weight,
                'value'     => $amount,
                'vendor'    => $vendor
            ));
            
            if (in_array(self::RATE_REQUEST_SMARTPOST, $allowedMethods)) {
                $response = $this->_doVendorRatesRequest(self::RATE_REQUEST_SMARTPOST,$vendorRequest);
                $preparedSmartpost = $this->_prepareVendorRateResponse($response,$vendor);
                if (!$preparedSmartpost->getError()) {
                    $this->_result->append($preparedSmartpost);
                }
            }
            // make general request for all methods
            $response = $this->_doVendorRatesRequest(self::RATE_REQUEST_GENERAL,$vendorRequest);
            $preparedGeneral = $this->_prepareVendorRateResponse($response,$vendor);
            if (!$preparedGeneral->getError() || ($this->_result->getError() && $preparedGeneral->getError())) {
                $this->_result->append($preparedGeneral);
            }
            
        }
        
        return $this->_result;
    }
    
    /**
     * Makes remote request to the carrier and returns a response
     *
     * @param string $purpose
     * @param Varien_Object $vendorRequest
     * @return mixed
     */
    protected function _doVendorRatesRequest($purpose, $vendorRequest){
        $ratesRequest = $this->_formVendorRateRequest($purpose,$vendorRequest);
        $requestString = serialize($ratesRequest);
        $response = $this->_getCachedQuotes($requestString);
        $debugData = array('request' => $ratesRequest);
        if ($response === null) {
            try {
                $client = $this->_createRateSoapClient();
                $response = $client->getRates($ratesRequest);
                $this->_setCachedQuotes($requestString, serialize($response));
                $debugData['result'] = $response;
            } catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                Mage::logException($e);
            }
        } else {
            $response = unserialize($response);
            $debugData['result'] = $response;
        }
        $this->_debug($debugData);
        return $response;
    }
    
    
    /**
     * Forming request for rate estimation depending to the purpose
     *
     * @param string $purpose
     * @return array
     */
    protected function _formVendorRateRequest($purpose,$vendorRequest)
    {
        $r = $this->_rawRequest;
        $ratesRequest = array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => $r->getKey(),
                    'Password' => $r->getPassword()
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' => $r->getAccount(),
                'MeterNumber'   => $r->getMeterNumber()
            ),
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => array(
                'DropoffType'   => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => array(
                    'Amount'  => $vendorRequest->getValue(),
                    'Currency' => $this->getCurrencyCode()
                ),
                'Shipper' => array(
                    'Address' => array(
                        'PostalCode'  => $vendorRequest->getPostcode(),
                        'CountryCode' => $vendorRequest->getCountry()
                    )
                ),
                'Recipient' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => (bool)$this->getConfigData('residence_delivery')
                    )
                ),
                'ShippingChargesPayment' => array(
                    'PaymentType' => 'SENDER',
                    'Payor' => array(
                        'AccountNumber' => $r->getAccount(),
                        'CountryCode'   => $vendorRequest->getCountry()
                    )
                ),
                'CustomsClearanceDetail' => array(
                    'CustomsValue' => array(
                        'Amount' => $vendorRequest->getValue(),
                        'Currency' => $this->getCurrencyCode()
                    )
                ),
                'RateRequestTypes' => 'LIST',
                'PackageCount'     => '1',
                'PackageDetail'    => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => array(
                    '0' => array(
                        'Weight' => array(
                            'Value' => (float)$vendorRequest->getWeight(),
                            'Units' => $this->getConfigData('unit_of_measure')
                        ),
                        'GroupPackageCount' => 1,
                    )
                )
            )
        );
    
        if ($purpose == self::RATE_REQUEST_GENERAL) {
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = array(
                'Amount'  => $vendorRequest->getValue(),
                'Currency' => $this->getCurrencyCode()
            );
        } else if ($purpose == self::RATE_REQUEST_SMARTPOST) {
            $ratesRequest['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
            $ratesRequest['RequestedShipment']['SmartPostDetail'] = array(
                'Indicia' => ((float)$r->getWeight() >= 1) ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getConfigData('smartpost_hubid')
            );
        }
    
        return $ratesRequest;
    }
    
    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @param VES_Vendors_Model_Vendor $vendor
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _prepareVendorRateResponse($response,$vendor){
        $costArr = array();
        $priceArr = array();
        $errorTitle = 'Unable to retrieve tracking';
        
        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                if (is_array($response->Notifications)) {
                    $notification = array_pop($response->Notifications);
                    $errorTitle = (string)$notification->Message;
                } else {
                    $errorTitle = (string)$response->Notifications->Message;
                }
            } elseif (isset($response->RateReplyDetails)) {
                $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
        
                if (is_array($response->RateReplyDetails)) {
                    foreach ($response->RateReplyDetails as $rate) {
                        $serviceName = (string)$rate->ServiceType;
                        if (in_array($serviceName, $allowedMethods)) {
                            $amount = $this->_getRateAmountOriginBased($rate);
                            $costArr[$serviceName]  = $amount;
                            $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                        }
                    }
                    asort($priceArr);
                } else {
                    $rate = $response->RateReplyDetails;
                    $serviceName = (string)$rate->ServiceType;
                    if (in_array($serviceName, $allowedMethods)) {
                        $amount = $this->_getRateAmountOriginBased($rate);
                        $costArr[$serviceName]  = $amount;
                        $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                    }
                }
            }
        }
        
        $result = Mage::getModel('shipping/rate_result');
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier($this->_code);
                $rate->setVendorId($vendor->getId());
                $rate->setCarrierTitle($this->getConfigData('title'));
                //$rate->setMethod($method);
                $rate->setMethod($method.VES_VendorsShipping_Model_Shipping::DELEMITER.$vendor->getId()); /*This is important*/
                $rate->setMethodTitle($this->getCode('method', $method));
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
        return $result;
    }
}
