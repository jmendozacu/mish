<?php
class VES_Vendors_Model_Resource_Additional extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("vendors/additional", "vendor_additional_id");
    }
}