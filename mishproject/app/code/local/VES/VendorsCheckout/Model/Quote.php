<?php

class VES_VendorsCheckout_Model_Quote extends Mage_Sales_Model_Quote{
	
	public function _getResource(){
		return Mage::getResourceModel('vendorscheckout/quote');
	}
	
	
	/**
     * Loading quote data by customer
     *
     * @return Mage_Sales_Model_Quote
     */
    public function loadQuoteByCustomer($customer, $vendorId)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customerId = $customer->getId();
        }
        else {
            $customerId = (int) $customer;
        }
        $this->_getResource()->loadQuoteByCustomerId($this, $customerId,$vendorId);
        $this->_afterLoad();
        return $this;
    }
}
