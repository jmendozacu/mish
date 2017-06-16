<?php

class VES_VendorsSubAccount_Model_Role extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorssubaccount/role');
    }

    public function getRoleResources(){
    	return explode(",", $this->getData('resources'));
    }
}