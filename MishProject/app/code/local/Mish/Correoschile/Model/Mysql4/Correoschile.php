<?php

class Mish_Correoschile_Model_Mysql4_Correoschile extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the correoschile_id refers to the key field in your database table.
        $this->_init('correoschile/correoschile', 'correoschile_id');
    }
}