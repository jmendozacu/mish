<?php

class VES_VendorsRma_Model_Resource_Status extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the vendorsrma_id refers to the key field in your database table.
        $this->_init('vendorsrma/status', 'status_id');
    }
}