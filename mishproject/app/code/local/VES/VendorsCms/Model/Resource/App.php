<?php

class VES_VendorsCms_Model_Resource_App extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorscms/app', 'app_id');
    }
    
	/**
     * Delete all app options
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('vendorscms/appoption');
        $query = "DELETE FROM {$table} WHERE app_id = {$object->getId()}";
        $writeConnection->query($query);
        return $this;
    }
    
}