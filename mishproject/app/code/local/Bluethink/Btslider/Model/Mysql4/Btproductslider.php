<?php

class Bluethink_Btslider_Model_Mysql4_Btproductslider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the btslider_id refers to the key field in your database table.
        $this->_init('btslider/btproductslider', 'bt_productslider_id');
    }
}