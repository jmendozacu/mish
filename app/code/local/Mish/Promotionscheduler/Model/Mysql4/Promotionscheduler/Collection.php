<?php

class Mish_Promotionscheduler_Model_Mysql4_Promotionscheduler_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('promotionscheduler/promotionscheduler');
    }
}