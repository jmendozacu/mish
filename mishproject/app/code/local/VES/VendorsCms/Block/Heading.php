<?php
class VES_VendorsCms_Block_Heading extends Mage_Core_Block_Template{
	public function getPage(){
		return Mage::registry('vendorscms_page');
	}
	public function getContentHeading(){
		return $this->getPage()->getContentHeading();
	}
}