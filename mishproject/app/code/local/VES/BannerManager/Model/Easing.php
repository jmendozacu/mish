<?php

class VES_BannerManager_Model_Easing extends Varien_Object
{
	static public function getOptionArray()
    {
		$options[] = array(
			'label' => Mage::helper('bannermanager')->__('Prototype Slider'),
			'value' => $prototype
		);
		
		
		$options[] = array(
			'label' => Mage::helper('bannermanager')->__('Nivo Slider (jQuery)'),
			'value' => Mage::helper('bannermanager')->getEffectOptionsData()
		);
		
    	return $options;
    }
}