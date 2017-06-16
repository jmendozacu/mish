<?php


class VES_VendorsVacation_Model_Mysql4_Vacation_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsvacation/vacation');
    }
}