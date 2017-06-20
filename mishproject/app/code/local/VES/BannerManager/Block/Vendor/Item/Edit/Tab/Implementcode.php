<?php

class VES_BannerManager_Block_Vendor_Item_Edit_Tab_Implementcode extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct(){
		$this->setTemplate('ves_bannermanager/item_implementcode.phtml');
		//Mage::helper('ves_core');
	}
	
	public function getBanner()
	{
		return Mage::registry('bannermanager_data');
	}
}