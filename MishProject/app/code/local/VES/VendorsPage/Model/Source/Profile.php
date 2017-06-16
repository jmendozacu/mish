<?php

class VES_VendorsPage_Model_Source_Profile
{
	const POSITION_LEFT 	= 'left';
	const POSITION_RIGHT 	= 'right';
	const POSITION_CONTENT 	= 'content';
	
	public function toOptionArray()
    {
    	$positions =  array(
			array(
            	'label' => Mage::helper('vendorspage')->__('Left'),
                'value' => self::POSITION_LEFT,
			),
			array(
            	'label' => Mage::helper('vendorspage')->__('Right'),
                'value' => self::POSITION_RIGHT,
			),
			array(
            	'label' => Mage::helper('vendorspage')->__('Content'),
                'value' => self::POSITION_CONTENT,
			),
		);
		$positions = new Varien_Object($positions);
		
		Mage::dispatchEvent('ves_vendors_profile_prepare_position',array('positions'=>$positions));
		return $positions->getData();
    }
}