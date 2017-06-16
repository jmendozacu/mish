<?php

class Bluethink_Btslider_Model_Mysql4_Btslider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the btslider_id refers to the key field in your database table.
        $this->_init('btslider/btslider', 'btslider_id');
    }
}