<?php

class VES_VendorsVacation_Model_Source_Product extends Varien_Object
{
    const PRODUCT_YES 		        = 1;
    const PRODUCT_NO 	            = 0;

    static public function getOptionArray()
    {
        return array(
            self::PRODUCT_YES    		        => Mage::helper("vendorsvacation")->__("Yes"),
            self::PRODUCT_NO   		            => Mage::helper("vendorsvacation")->__("No"),
        );
    }

    public function toOptionArray()
    {
        return array(
            array('value' => self::PRODUCT_YES, 'label'=>Mage::helper('vendorsvacation')->__('Yes')),
            array('value' => self::PRODUCT_NO, 'label'=>Mage::helper('vendorsvacation')->__('No')),
        );
    }
}