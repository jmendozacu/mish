<?php

class Mish_Correoschile_Model_Correosmaintable extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('correoschile/correosmaintable');
    }
}