<?php

class Mish_Shipit_Model_Mysql4_Shipit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the shipit_id refers to the key field in your database table.
        $this->_init('shipit/shipit', 'shipit_id');
    }
}