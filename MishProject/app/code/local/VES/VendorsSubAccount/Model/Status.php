<?php

class VES_VendorsSubAccount_Model_Status extends Varien_Object
{

    static public function getOptionArray()
    {
        return array(
            VES_VendorsSubAccount_Model_Account::STATUS_ENABLED    => Mage::helper('vendorssubaccount')->__('Enabled'),
            VES_VendorsSubAccount_Model_Account::STATUS_DISABLED   => Mage::helper('vendorssubaccount')->__('Disabled')
        );
    }
    
	static public function toOptionArray()
    {
        return array(
        	array(
        		'value'     => VES_VendorsSubAccount_Model_Account::STATUS_ENABLED,
				'label'     => Mage::helper('vendorssubaccount')->__('Enabled'),
        	),
        	array(
        		'value'     => VES_VendorsSubAccount_Model_Account::STATUS_DISABLED,
				'label'     => Mage::helper('vendorssubaccount')->__('Disabled'),
        	),
        );
    }
}