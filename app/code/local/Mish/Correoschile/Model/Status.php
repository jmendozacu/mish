<?php

class Mish_Correoschile_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('correoschile')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('correoschile')->__('Disabled')
        );
    }
}