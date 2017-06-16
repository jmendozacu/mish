<?php 
class VES_VendorsFlatrate_Block_Vendor_Notification extends Mage_Core_Block_Template{
	protected function _getVendorSession(){
		return Mage::getSingleton('vendors/session');
	}
	
	public function _toHtml(){
		$vendor = $this->_getVendorSession()->getVendor();
		if(!Mage::helper('vendorsflatrate')->enableFlatrateShipping() || !Mage::helper('vendorsconfig')->getVendorConfig('shipping/flatrate/active',$vendor->getId())) return '';
		$rates = Mage::helper('vendorsconfig')->getVendorConfig('shipping/flatrate/rates',$vendor->getId());
		if($rates){
			$rates = unserialize($rates);
			if(sizeof($rates)) return '';
		}
		
		return parent::_toHtml();
	}
}