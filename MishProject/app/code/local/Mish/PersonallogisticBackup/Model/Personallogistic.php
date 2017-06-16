<?php

class Mish_Personallogistic_Model_Personallogistic extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('personallogistic/personallogistic');
    }
}