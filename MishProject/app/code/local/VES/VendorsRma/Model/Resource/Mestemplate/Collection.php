<?php

class VES_VendorsRma_Model_Resource_Mestemplate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsrma/mestemplate');
    }
}