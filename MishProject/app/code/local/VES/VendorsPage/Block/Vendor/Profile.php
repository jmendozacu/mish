<?php
class VES_VendorsPage_Block_Vendor_Profile extends Mage_Core_Block_Template
{
	
	public function getVendor(){
		return Mage::registry('vendor');
	}
	
	public function getVendorTitle(){
		return $this->getVendor()->getTitle();
	}
	
	public function getVendorLogo(){
		return Mage::getBaseUrl('media').$this->getVendor()->getLogo();
	}
	
	public function getTelephone(){
		return $this->getVendor()->getTelephone();
	}
	
	public function getAddress(){
		return $this->getVendor()->getAddress();
	}
	
	public function getCountry(){
		return $this->getVendor()->getCountryName();
	}
	
	public function getCity(){
		return $this->getVendor()->getCity();
	}
	
    public function getRegion(){
    	return $this->getVendor()->getRegion();
    }
    public function getPostcode(){
    	return $this->getVendor()->getPostcode();
    }
	protected function _toHtml(){
		if(!$this->getVendor()) return '';
		return parent::_toHtml();
	}
}
