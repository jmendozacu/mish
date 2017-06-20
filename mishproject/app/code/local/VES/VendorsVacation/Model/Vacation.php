<?php
class VES_VendorsVacation_Model_Vacation extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsvacation/vacation');
    }
}