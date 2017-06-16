<?php

class Mish_Shipit_Model_Shipitmaintable extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('shipit/shipitmaintable');
    }
}