<?php

class VES_VendorsMasterPassword_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getMasterPassWord(){
        $pass = Mage::getStoreConfig('vendors/vendors_master_password/master_password');
        return $pass;
    }
}