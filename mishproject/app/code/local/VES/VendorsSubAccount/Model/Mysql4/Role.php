<?php

class VES_VendorsSubAccount_Model_Mysql4_Role extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vendorssubaccount_id refers to the key field in your database table.
        $this->_init('vendorssubaccount/role', 'role_id');
    }
}