<?php

class VES_VendorsInventory_Helper_Adjuststock extends Mage_Core_Helper_Abstract {

    /**
     * Check permission to adjust stock.
     * 
     * @return boolean
     */
    public function getWarehouse() {
        $adminId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $warehouseIds = array();
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $adminId)
                ->addFieldToFilter('can_adjust', 1);
        foreach ($collection as $assignment) {
            $warehouseIds[] = $assignment->getWarehouseId();
        }
        $warehouseCollection = Mage::getModel('inventoryplus/warehouse')->getCollection()
                ->addFieldToFilter('warehouse_id', array('in' => $warehouseIds));
        if ($warehouseCollection->getSize()) {
            return true;
        }
        return false;
    }

    public function getWarehouseByAdmin() {
        $adminId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $warehouseIds = array();
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $adminId)
                ->addFieldToFilter('can_adjust', 1);
        foreach ($collection as $assignment) {
            $warehouseIds[] = $assignment->getWarehouseId();
        }
        $warehouseCollection = Mage::getModel('inventoryplus/warehouse')->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('warehouse_id', array('in' => $warehouseIds));
        return $warehouseCollection;
    }

    public function isWarehouseEnabled() {
        return Mage::helper('core')->isModuleEnabled('Magestore_Inventorywarehouse');
    }

    public function getPermission($warehouseId, $permissionType) { 
        $admin = Mage::getSingleton('vendors/session')->getUser()->getId();
        $result = Mage::getResourceModel('vendorsinventory/permission')
                ->getPermission($admin, $warehouseId);
        $permission = isset($result[$permissionType]) ? $result[$permissionType] : 0;
        return $permission;
    }

    public function getUploadFile() {
        return $_FILES;
    }

    /**
     * Import data for product grid
     * 
     * @return null
     */
    public function importProduct($data) {
        if (count($data)) {
            Mage::getModel('vendors/session')->setData('adjuststock_product_import', $data);
        }
    }

}
