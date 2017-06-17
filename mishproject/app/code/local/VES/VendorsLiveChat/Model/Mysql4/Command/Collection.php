<?php

class VES_VendorsLiveChat_Model_Mysql4_Command_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorslivechat/command');
    }
}