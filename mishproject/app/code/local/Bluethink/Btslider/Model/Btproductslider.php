<?php

class Bluethink_Btslider_Model_Btproductslider extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('btslider/btproductslider');
    }
}