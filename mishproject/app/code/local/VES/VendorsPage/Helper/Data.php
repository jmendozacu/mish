<?php

class VES_VendorsPage_Helper_Data extends Mage_Core_Helper_Abstract
{
	const VENDOR_LAYOUT_HANDLE = 'vendor_page';
	/**
	 * Get position of profile block
	 */
	public function getProfileBlockPosition(){
		return Mage::helper('vendorsconfig')->getVendorConfig('design/config/vendor_profile',Mage::registry('vendor')->getId());
	}
	
	/**
	 * Get product list mode
	 */
	public function getProductListMode(){
		return Mage::helper('vendorsconfig')->getVendorConfig('design/home/list_mode',Mage::registry('vendor')->getId());
	}
	/**
	 * Get Vendor Url
	 * @param string|VES_Vendors_Model_Vendor $vendor
	 * @param string $urlKey
	 * @parem array $param
	 */
	public function getUrl($vendor, $urlKey='',$param =  array()){
		$vendorId = $vendor;
		if($vendor instanceof VES_Vendors_Model_Vendor){
			$vendorId = $vendor->getVendorId();
		}
		$baseUrlKey = Mage::getStoreConfig('vendors/vendor_page/url_key');
		$tmpUrl = Mage::getUrl($urlKey,$param);
		
		if($baseUrlKey){
			return str_replace(Mage::getUrl('',array('_nosid'=>1)), Mage::getUrl($baseUrlKey.'/'.$vendorId,array('_nosid'=>1)), $tmpUrl);
		}
		
		return str_replace(Mage::getUrl('',array('_nosid'=>1)), Mage::getUrl($vendorId,array('_nosid'=>1)), $tmpUrl);
	}
	
	/**
	 * Get top link url of vendor home page
	 */
	public function getVendorHomePageUrl(){
		$vendor = Mage::getSingleton('vendors/session')->getVendor();
		return $this->getUrl($vendor);
	}
}