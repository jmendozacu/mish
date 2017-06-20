<?php

class VES_VendorsLiveChat_Model_Contact extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorslivechat/contact');
    }
}