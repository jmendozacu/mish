<?php

class VES_VendorsReview_Model_Source_Function
{
	public function toOptionArray()
    {
    	return array(
    		array(
    				'label'	=> 	Mage::helper('vendorsreview')->__('Delete'),
    				'value'	=> 'delete',
    		),
    		array(
            	'label' => Mage::helper('vendorsreview')->__('Save'),
                'value' => 'save',
			),
		);
    }
}