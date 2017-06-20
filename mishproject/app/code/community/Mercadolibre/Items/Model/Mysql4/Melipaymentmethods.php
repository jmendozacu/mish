<?php

class MercadoLibre_Items_Model_Mysql4_Melipaymentmethods extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('items/melipaymentmethods', 'id');
    }
}