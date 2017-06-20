<?php

class VES_VendorsCategory_Model_Source_Layout
{
	public function toOptionArray()
    {
    	return array(
    		array(
    			'label'	=> 	Mage::helper('vendorscategory')->__('No layout updates'),
    			'value'	=> '',	
    		),
    		array(
    				'label'	=> 	Mage::helper('vendorscategory')->__('Empty'),
    				'value'	=> 'empty',
    		),
    		array(
            	'label' => Mage::helper('vendorscategory')->__('1 Column'),
                'value' => '1column',
			),
			array(
            	'label' => Mage::helper('vendorscategory')->__('2 Columns Left'),
                'value' => '2columns-left',
			),
			array(
            	'label' => Mage::helper('vendorscategory')->__('2 Columns Right'),
                'value' => '2columns-right',
			),
			array(
            	'label' => Mage::helper('vendorscategory')->__('3 Columns'),
                'value' => '3columns',
			),
		);
    }
}