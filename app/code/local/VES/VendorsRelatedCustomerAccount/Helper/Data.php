<?php

class VES_VendorsRelatedCustomerAccount_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getVendorUrl(){
		return Mage::getUrl('customer/vendor');
	}
}