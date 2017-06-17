<?php

class Mish_Blueexpress_Model_Mysql4_Blueexpress_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blueexpress/blueexpress');
    }
}