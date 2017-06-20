<?php
class VES_VendorsCms_Block_Page_Html_Header extends Mage_Page_Block_Html_Header
{
	public function _construct()
    {
    	if(!Mage::helper('vendorscms')->moduleEnable() || !$this->getVendor()) return parent::_construct();
        $this->setTemplate('ves_vendorscms/page/html/header.phtml');
    }
    
	public function getVendor(){
		return Mage::registry('vendor');
	}
	
	public function getLogoSrc(){
		if(!$this->getVendor() || !Mage::helper('vendorscms')->moduleEnable()){
			return parent::getLogoSrc();
		}
		return Mage::getBaseUrl('media').$this->getVendor()->getLogo();
	}
	
	public function getLogoUrl(){
		if(!$this->getVendor() || !Mage::helper('vendorscms')->moduleEnable()){
			return $this->getUrl('');
		}
		return Mage::helper('vendorspage')->getUrl($this->getVendor()->getVendorId());
	}
}
