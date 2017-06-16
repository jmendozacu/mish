<?php

class VES_Commision_Model_Commision extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('commision/commision');
    }
}