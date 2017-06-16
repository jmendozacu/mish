<?php

class VES_VendorsRma_Model_Option_Pack extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 0;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('vendorsrma')->__('Yes'),
            self::STATUS_DISABLED   => Mage::helper('vendorsrma')->__('No')
        );
    }

    static public function getOptions(){
        return
            array(
                array(
                    'value'     => self::STATUS_ENABLED,
                    'label'     => Mage::helper('vendorsrma')->__('Yes'),
                ),

                array(
                    'value'     => self::STATUS_DISABLED,
                    'label'     => Mage::helper('vendorsrma')->__('No'),
                ),
            );
    }

    public function getTitleByKey($key){
        if($key == self::STATUS_DISABLED) return Mage::helper('vendorsrma')->__('No');
        return Mage::helper('vendorsrma')->__('Yes');
    }
}