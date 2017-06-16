<?php

class VES_VendorsRma_Model_Option_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 0;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('vendorsrma')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('vendorsrma')->__('Disabled')
        );
    }

    static public function getOptions(){
        return
        array(
            array(
                'value'     => self::STATUS_ENABLED,
                'label'     => Mage::helper('vendorsrma')->__('Enabled'),
            ),

            array(
                'value'     => self::STATUS_DISABLED,
                'label'     => Mage::helper('vendorsrma')->__('Disabled'),
            ),
        );
    }
}