<?php

class VES_BannerManager_Model_Yesno extends Varien_Object
{

    static public function getOptionArray()
    {
        return array(
            1    => Mage::helper('bannermanager')->__('Yes'),
            0   => Mage::helper('bannermanager')->__('No')
        );
    }
}