<?php

class VES_VendorsVacation_Model_Source_Vacation extends Varien_Object
{
    const VACATION_ON 		        = 1;
    const VACATION_OFF 	            = 0;

    static public function getOptionArray()
    {
        return array(
            self::VACATION_ON    		        => Mage::helper("vendorsvacation")->__("On"),
            self::VACATION_OFF   		        => Mage::helper("vendorsvacation")->__("Off"),
        );
    }

    public function toOptionArray()
    {
        return array(
            array('value' => self::VACATION_ON, 'label'=>Mage::helper('vendorsvacation')->__('On')),
            array('value' => self::VACATION_OFF, 'label'=>Mage::helper('vendorsvacation')->__('Off')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
//    public function toArray()
//    {
//        return array(
//            self::VACATION_ON   => Mage::helper('vendorsvacation')->__('On'),
//            self::VACATION_OFF  => Mage::helper('vendorsvacation')->__('Off'),
//        );
//    }
}