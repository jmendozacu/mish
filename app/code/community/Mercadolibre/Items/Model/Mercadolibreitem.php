<?php

class Mercadolibre_Items_Model_Mercadolibreitem extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('items/Mercadolibreitem');
    }
}