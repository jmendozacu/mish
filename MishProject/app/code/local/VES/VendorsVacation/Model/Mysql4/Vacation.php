<?php
class VES_VendorsVacation_Model_Mysql4_Vacation extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('vendorsvacation/vacation', 'vacation_id');
    }

}