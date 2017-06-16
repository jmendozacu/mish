<?php

class Evince_WhatsApp_Block_Whatsapp extends Mage_Catalog_Block_Product_View
{
	public function getButtonSize(){
		return Mage::getStoreConfig('evince_dev_section/evince_dev_group/evince_dev_button');
	}
	
	public function getButtonPos(){
		return Mage::getStoreConfig('evince_dev_section/evince_dev_group/evince_dev_button_pos');
	}
	
	public function getIsEnable()
	{
		return Mage::getStoreConfig('evince_dev_section/evince_dev_group/evince_dev_enable');
		
	}

	public function getCustomShareMessage()
	{
		return Mage::getStoreConfig('evince_dev_section/evince_dev_group/evince_dev_custom_share_message');
		
	}


	public function getBackcolor()
	{
		return Mage::getStoreConfig('evince_dev_section/evince_dev_group/evnc_background');
	
	}
}