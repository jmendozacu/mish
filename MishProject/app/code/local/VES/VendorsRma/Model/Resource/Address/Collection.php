<?php

class VES_VendorsRma_Model_Resource_Address_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsrma/address');
    }
}