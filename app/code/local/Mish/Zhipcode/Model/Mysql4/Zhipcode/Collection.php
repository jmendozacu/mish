<?php

class Mish_Zhipcode_Model_Mysql4_Zhipcode_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('zhipcode/zhipcode');
    }
}