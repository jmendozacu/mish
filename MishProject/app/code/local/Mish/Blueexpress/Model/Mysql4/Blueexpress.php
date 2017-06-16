<?php

class Mish_Blueexpress_Model_Mysql4_Blueexpress extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the blueexpress_id refers to the key field in your database table.
        $this->_init('blueexpress/blueexpress', 'blueexpress_id');
    }
}