<?php

class MercadoLibre_Items_Model_Mysql4_Melibuyer extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the itemlisting_id refers to the key field in your database table.
        $this->_init('items/melibuyer', 'id');
    }
}