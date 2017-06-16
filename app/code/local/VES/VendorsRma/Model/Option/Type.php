<?php

class VES_VendorsRma_Model_Option_Type extends Varien_Object
{
    const STATUS_VENDOR	= 1;
    const STATUS_CUSTOMER	= 0;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_CUSTOMER    => Mage::helper('vendorsrma')->__('Customer'),
            self::STATUS_VENDOR   => Mage::helper('vendorsrma')->__('Vendor')
        );
    }

    static public function getOptions(){
        return
            array(
                array(
                    'value'     => self::STATUS_CUSTOMER,
                    'label'     => Mage::helper('vendorsrma')->__('Customer'),
                ),

                array(
                    'value'     => self::STATUS_VENDOR,
                    'label'     => Mage::helper('vendorsrma')->__('Vendor'),
                ),
            );
    }

    public function getTitleByKey($key){
        if($key == self::STATUS_CUSTOMER) return Mage::helper('vendorsrma')->__('Customer');
        return Mage::helper('vendorsrma')->__('STATUS_VENDOR');
    }
}