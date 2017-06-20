<?php

class VES_Vendors_Model_Source_Status extends Varien_Object
{
    static public function getOptionArray()
    {
        return array(
            VES_Vendors_Model_Vendor::STATUS_PENDING    	=> Mage::helper('vendors')->__('Pending'),
            VES_Vendors_Model_Vendor::STATUS_ACTIVATED    	=> Mage::helper('vendors')->__('Approved'),
            VES_Vendors_Model_Vendor::STATUS_DISABLED       => Mage::helper('vendors')->__('Disabled'),
            VES_Vendors_Model_Vendor::STATUS_REJECTED  		=> Mage::helper('vendors')->__('Rejected'),
        );
    }
    
	public function getAllOptions()
    {
    	return array(
			array(
            	'label' => Mage::helper('vendors')->__('Pending'),
                'value' =>  VES_Vendors_Model_Vendor::STATUS_PENDING
			),
            array(
            	'label' => Mage::helper('vendors')->__('Activated'),
                'value' =>  VES_Vendors_Model_Vendor::STATUS_ACTIVATED
           	),
           	array(
            	'label' => Mage::helper('vendors')->__('Disabled'),
                'value' =>  VES_Vendors_Model_Vendor::STATUS_DISABLED
           	),
            array(
                'label' => Mage::helper('vendors')->__('Rejected'),
                'value' =>  VES_Vendors_Model_Vendor::STATUS_REJECTED
            ),
		);
    }
}