<?php
class VES_VendorsCredit_Model_Resource_Payment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
    {
        $this->_init('vendorscredit/payment');
    }
}