<?php

class VES_Commision_Model_Mysql4_Vendorcommision_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('commision/vendorcommision');
    }
}