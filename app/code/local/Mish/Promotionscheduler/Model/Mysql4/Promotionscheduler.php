<?php

class Mish_Promotionscheduler_Model_Mysql4_Promotionscheduler extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the promotionscheduler_id refers to the key field in your database table.
        $this->_init('promotionscheduler/promotionscheduler', 'promotionscheduler_id');
    }
}