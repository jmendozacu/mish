<?php

class VES_VendorsContact_Model_VendorsContact extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscontact/vendorscontact');
    }
}