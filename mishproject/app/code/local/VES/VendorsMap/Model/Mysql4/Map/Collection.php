<?php

class VES_VendorsMap_Model_Mysql4_Map_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsmap/map');
    }
}