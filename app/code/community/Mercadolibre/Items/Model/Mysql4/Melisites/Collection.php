<?php

class Mercadolibre_Items_Model_Mysql4_Melisites_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('items/melisites');
    }
}