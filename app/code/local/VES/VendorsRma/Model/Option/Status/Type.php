<?php

class VES_VendorsRma_Model_Option_Status_Type extends Varien_Object
{
    const TYPE_ADMIN	= 1;
    const TYPE_VENDOR_CUSTOMER	= 0;
    const TYPE_VENDOR	= 2;
    const TYPE_CUSTOMER	= 3;

    static public function getOptionArray()
    {
        return array(
            self::TYPE_ADMIN    => Mage::helper('vendorsrma')->__('Admin'),
            self::TYPE_VENDOR_CUSTOMER   => Mage::helper('vendorsrma')->__('Vendor and Customer'),
            self::TYPE_VENDOR   => Mage::helper('vendorsrma')->__('Vendor'),
            self::TYPE_CUSTOMER   => Mage::helper('vendorsrma')->__('Customer'),
        );
    }

    static public function getOptions(){
        return
            array(
                array(
                    'value'     => self::TYPE_ADMIN,
                    'label'     => Mage::helper('vendorsrma')->__('Admin'),
                ),
                array(
                    'value'     => self::TYPE_VENDOR,
                    'label'     => Mage::helper('vendorsrma')->__('Vendor'),
                ),
                array(
                    'value'     => self::TYPE_CUSTOMER,
                    'label'     => Mage::helper('vendorsrma')->__('Customer'),
                ),
                array(
                    'value'     => self::TYPE_VENDOR_CUSTOMER,
                    'label'     => Mage::helper('vendorsrma')->__('Vendor and Customer'),
                ),
            );
    }

    public function getTitleByKey($key){
        if($key == self::TYPE_VENDOR) return Mage::helper('vendorsrma')->__('Vendor');
        if($key == self::TYPE_CUSTOMER) return Mage::helper('vendorsrma')->__('Customer');
        if($key == self::TYPE_ADMIN) return Mage::helper('vendorsrma')->__('Admin');
        return Mage::helper('vendorsrma')->__('Vendor and Customer');
    }
}