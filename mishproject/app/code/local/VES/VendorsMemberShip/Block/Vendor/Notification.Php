<?php
class VES_VendorsMemberShip_Block_Vendor_Notification extends Mage_Core_Block_Template
{
	protected function _getVendorSession(){
		return Mage::getSingleton('vendors/session');
	}
	
	public function getExpiryDate(){
		return Mage::app()->getLocale()->date(strtotime($this->_getVendorSession()->getVendor()->getExpiryDate()))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM));
	}
	
	public function getUpgradeUrl(){
		return Mage::getStoreConfig('vendors/create_account/upgrade_url');
	}
	
	public function _toHtml(){
		$vendor = $this->_getVendorSession()->getVendor();
		$expiryDate = $vendor->getExpiryDate();
		
		$showNotification = date('Y-m-d', strtotime(now(). ' + 7 day')) > $expiryDate;
		if($vendor && $showNotification){
			return parent::_toHtml();
		}
		return '';
	}
}