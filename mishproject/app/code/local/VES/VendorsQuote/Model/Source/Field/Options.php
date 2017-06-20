<?php

class VES_VendorsQuote_Model_Source_Field_Options extends Varien_Object
{   
	public function getAllOptions()
    {
    	return array(
			array(
            	'label' => Mage::helper('vendorsquote')->__('No'),
                'value' =>  0
			),
            array(
            	'label' => Mage::helper('vendorsquote')->__('Yes'),
                'value' =>  1
           	),
    	    array(
    	        'label' => Mage::helper('vendorsquote')->__('Yes and Required'),
    	        'value' =>  1
    	    ),
		);
    }
    
    public function toOptionArray()
    {
        return array(
            0 => Mage::helper('vendorsquote')->__('No'),
            1 => Mage::helper('vendorsquote')->__('Yes'),
            2 => Mage::helper('vendorsquote')->__('Yes and Required'),
        );
    }
}