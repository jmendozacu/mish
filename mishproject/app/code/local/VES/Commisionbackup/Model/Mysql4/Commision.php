<?php

class VES_Commision_Model_Mysql4_Commision extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the commision_id refers to the key field in your database table.
        $this->_init('commision/commision', 'commision_id');
    }
}