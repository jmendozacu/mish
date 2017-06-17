<?php

class VES_VendorsRma_Model_Resource_Request_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsrma/request');
    }
}