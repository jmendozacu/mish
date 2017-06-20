<?php

class VES_VendorsReview_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function getVendorUrlKey() {
		return Mage::getStoreConfig('vendors/vendor_page/url_key');
	}
	
	public function getVendorRatingUrl($vendor, $order=null) {
		if(!$order) {
			return Mage::helper('vendorspage')->getUrl($vendor,'rating/index/');
		}
		return Mage::helper('vendorspage')->getUrl($vendor,'rating/index'). '/order_id/'.$order;
	}
	
	
	
	public function isCustomerSendEmail() {
		return Mage::getStoreConfig('vendors/vendorsreview/send_review_mail');
	}
	
	public function getAdminFunctions() {
		return Mage::getStoreConfig('vendors/vendorsreview/admin_functions');
	}
}