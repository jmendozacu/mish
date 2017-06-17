<?php

class VES_BannerManager_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the bannermanager_id refers to the key field in your database table.
        $this->_init('bannermanager/item', 'item_id');
    }
}