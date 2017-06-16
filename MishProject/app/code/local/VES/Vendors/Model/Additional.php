<?php

class VES_Vendors_Model_Additional extends Mage_Core_Model_Abstract
{   
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendors/additional');
    }    
   
}