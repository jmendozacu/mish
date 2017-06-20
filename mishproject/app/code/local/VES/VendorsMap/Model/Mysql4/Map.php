<?php

class VES_VendorsMap_Model_Mysql4_Map extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vendorsmap_id refers to the key field in your database table.
        $this->_init('vendorsmap/map', 'map_id');
    }
}