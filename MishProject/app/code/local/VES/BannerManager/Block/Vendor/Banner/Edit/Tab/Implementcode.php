<?php

class VES_BannerManager_Block_Vendor_Banner_Edit_Tab_Implementcode extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct(){
		$this->setTemplate('ves_bannermanager/banner_implementcode.phtml');
		//Mage::helper('ves_core');
	}
	
	public function getBanner()
	{
		return Mage::registry('bannermanager_data');
	}
}