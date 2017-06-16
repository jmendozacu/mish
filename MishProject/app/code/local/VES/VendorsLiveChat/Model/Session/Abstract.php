<?php

class VES_VendorsLiveChat_Model_Session_Abstract extends Mage_Core_Model_Session_Abstract
{
    public function __construct() {
        $namespace = 'vendorslivechat';
//        $namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());

        $this->init($namespace);
        Mage::dispatchEvent('vendorslivechat_session_init', array('vendorslivechat_session' => $this));
    }
}