<?php

class VES_VendorsPage_Model_Source_Layout
{
	public function toOptionArray()
    {
    	return array(
    		array(
            	'label' => Mage::helper('vendorspage')->__('1 Column'),
                'value' => '1column',
			),
			array(
            	'label' => Mage::helper('vendorspage')->__('2 Columns Left'),
                'value' => '2columns-left',
			),
			array(
            	'label' => Mage::helper('vendorspage')->__('2 Columns Right'),
                'value' => '2columns-right',
			),
			array(
            	'label' => Mage::helper('vendorspage')->__('3 Columns'),
                'value' => '3columns',
			),
		);
    }
}