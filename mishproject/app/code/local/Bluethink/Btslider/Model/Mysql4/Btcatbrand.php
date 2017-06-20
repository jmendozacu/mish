<?php

class Bluethink_Btslider_Model_Mysql4_Btcatbrand extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the catbrand_id refers to the key field in your database table.
        $this->_init('btslider/btcatbrand', 'catbrand_id');
    }
}