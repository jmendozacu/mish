<?php

class VES_VendorsPaypal_Model_Standard extends Mage_Paypal_Model_Standard
{
    CONST SEPARATED_CHAR = '__';
    protected $_orders;
    /**
     * Aggregated cart summary label getter
     *
     * @return string
     */
    private function _getAggregatedCartSummary()
    {
        if ($this->_config->lineItemsSummary) {
            return $this->_config->lineItemsSummary;
        }
        return Mage::app()->getStore($this->getStore())->getFrontendName();
    }
    
    
    /**
     * Return form field array
     *
     * @return array
     */
    public function getStandardCheckoutFormFields()
    {
        $orderIds = $this->getCheckout()->getOrderIds();
        $order = null;
        
        $combinedOrderId = implode(self::SEPARATED_CHAR,$orderIds);

        foreach($orderIds as $id=>$incrementId){
            $order = Mage::getModel('sales/order')->load($id);
            $this->_orders[] = $order;
        }
        
        /* @var $api Mage_Paypal_Model_Api_Standard */
        $api = Mage::getModel('vendorspaypal/api_standard')->setConfigObject($this->getConfig());
        $api->setOrderId($combinedOrderId)
        ->setCurrencyCode($order->getBaseCurrencyCode())
        //->setPaymentAction()
        ->setOrder($order)
        ->setOrders($this->_orders)
        ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
        ->setReturnUrl(Mage::getUrl('paypal/standard/success'))
        ->setCancelUrl(Mage::getUrl('paypal/standard/cancel'));
    
        // export address
        $isOrderVirtual = $order->getIsVirtual();
        $address = $isOrderVirtual ? $order->getBillingAddress() : $order->getShippingAddress();
        if ($isOrderVirtual) {
            $api->setNoShipping(true);
        } elseif ($address->validate()) {
            $api->setAddress($address);
        }
    
        // add cart totals and line items
        $api->setPaypalCart(Mage::getModel('paypal/cart', array($order)))
        ->setIsLineItemsEnabled($this->_config->lineItemsEnabled)
        ;
        $api->setCartSummary($this->_getAggregatedCartSummary());
        $api->setLocale($api->getLocaleCode());
        $result = $api->getStandardCheckoutRequest();
        return $result;
    }
}