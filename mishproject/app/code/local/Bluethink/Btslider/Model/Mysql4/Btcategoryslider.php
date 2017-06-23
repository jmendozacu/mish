<?php

class Bluethink_Btslider_Model_Mysql4_Btcategoryslider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the btslider_id refers to the key field in your database table.
        $this->_init('btslider/btcategoryslider', 'btcatslider_id');
    }
}