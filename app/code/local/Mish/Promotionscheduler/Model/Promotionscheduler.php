<?php

class Mish_Promotionscheduler_Model_Promotionscheduler extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('promotionscheduler/promotionscheduler');
    }
}