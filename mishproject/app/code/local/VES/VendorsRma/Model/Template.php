<?php

class VES_VendorsRma_Model_Template extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsrma/template');
    }
}