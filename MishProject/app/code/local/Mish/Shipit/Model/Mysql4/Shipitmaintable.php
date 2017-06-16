<?php

class Mish_Shipit_Model_Mysql4_Shipitmaintable extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the id refers to the key field in your database table.
        $this->_init('shipit/shipitmaintable', 'id');
    }
}