<?php

class Bluethink_Btslider_Model_Mysql4_Btslider_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('btslider/btslider');
    }
}