<?php

class Mish_Personallogistic_Model_Mysql4_Personallogisticadmin_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('personallogistic/personallogisticadmin');
    }
}