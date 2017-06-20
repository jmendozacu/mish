<?php

class Mish_Zhipcode_Model_Mysql4_Zhipcode extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the zhipcode_id refers to the key field in your database table.
        $this->_init('zhipcode/zhipcode', 'zhipcode_id');
    }
}