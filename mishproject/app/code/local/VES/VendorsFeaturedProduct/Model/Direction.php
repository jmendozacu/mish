<?php

class VES_VendorsFeaturedProduct_Model_Direction extends Varien_Object
{

    static public function toOptionArray()
    {
        return array(
            'asc'    				=> Mage::helper('vendorsfeaturedproduct')->__('Ascending'),
            'desc'  			 	=> Mage::helper('vendorsfeaturedproduct')->__('Descending'),
        );
    }
}