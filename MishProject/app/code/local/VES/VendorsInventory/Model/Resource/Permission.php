<?php

class VES_VendorsInventory_Model_Resource_Permission extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() { 
        $this->_init('vendorsinventory/permission', 'warehouse_permission_id');
    }

    /**
     * Get warehouse permission
     * 
     * @param int $vendorId
     * @param int $warehouseId
     * @return array
     */
    public function getPermission($vendorId, $warehouseId) 
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('warehouse_id = :warehouse_id')
                ->where('vendor_id = :vendor_id');
        $bind = array(':warehouse_id' => $warehouseId, ':vendor_id' => $vendorId);

        $query = $adapter->query($select, $bind);
        while ($row = $query->fetch()) {
            return $row;
        }
    }
    
}
