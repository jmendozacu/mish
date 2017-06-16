<?php
class Evince_WhatsApp_Model_Whatsappposition extends Mage_Core_Model_Abstract
{
	public function toOptionArray()
	{
		return array(
	
				array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Product Detail Page View Section')),
				array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Product Page Footer Section')),
				array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('For Both Section')),
				
		);
	}
    
}