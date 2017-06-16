<?php

class Evince_WhatsApp_Model_Whatsapp extends Mage_Core_Model_Abstract
{
	
	public function toOptionArray()
	{
		return array(
	
				array('value' => s, 'label'=>Mage::helper('adminhtml')->__('Small')),
				array('value' => m, 'label'=>Mage::helper('adminhtml')->__('Medium')),
				array('value' => l, 'label'=>Mage::helper('adminhtml')->__('Large')),
				 
		);
	}
    
}