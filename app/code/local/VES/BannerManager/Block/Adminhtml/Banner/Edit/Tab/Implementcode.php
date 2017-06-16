<?php

class VES_BannerManager_Block_Adminhtml_Banner_Edit_Tab_Implementcode extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct(){
		$this->setTemplate('ves_bannermanager/implementcode.phtml');
		////Mage::helper('ves_core');
	}
	
	public function getBanner()
	{
		return Mage::registry('bannermanager_data');
	}
}