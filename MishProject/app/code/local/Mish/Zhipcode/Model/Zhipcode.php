<?php

class Mish_Zhipcode_Model_Zhipcode extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('zhipcode/zhipcode');
    }
}