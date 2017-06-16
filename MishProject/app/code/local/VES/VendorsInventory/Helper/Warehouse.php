<?php

class VES_VendorsInventory_Helper_Warehouse extends Mage_Core_Helper_Abstract {

    /**
     * Is Module Enable
     */
    public function canAdjust($vendorId, $warehouseId) {
        if ($warehouseId) {
            $assignmentCollection = Mage::getModel('vendorsinventory/permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('vendor_id', $vendorId);
            if ($assignmentCollection->getSize()) {
                $assignment = $assignmentCollection->setPageSize(1)->setCurPage(1)->getFirstItem();
                if ($assignment->getCanAdjust()) {
                    return true;
                }
            }
        } else {
            return true;
        }
        return false;
    }

    public function canSendAndRequest($vendorId, $warehouseId) {
        if ($warehouseId) {
            $assignmentCollection = Mage::getModel('vendorsinventory/permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('vendor_id', $vendorId);
            if ($assignmentCollection->getSize()) {
                $assignment = $assignmentCollection->setPageSize(1)->setCurPage(1)->getFirstItem();
                if ($assignment->getCanSendRequestStock()) {
                    return true;
                }
            }
        } else {
            return true;
        }
        return false;
    }

    public function canEdit($vendorId, $warehouseId) {
        if ($warehouseId) {
            $assignmentCollection = Mage::getModel('vendorsinventory/permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('vendor_id', $vendorId);
            if ($assignmentCollection->getSize()) {
                $assignment = $assignmentCollection->setPageSize(1)->setCurPage(1)->getFirstItem();
                if ($assignment->getCanEditWarehouse()) {
                    return true;
                }
            }
        } else {
            return true;
        }
        return false;
    }

    //check warehouse has not any product => can delete warehouse
    public function canDelete($warehouseId) {
        $warehouseProducts = Mage::getModel('inventoryplus/warehouse_product')
                ->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouseId)
                ->addFieldToFilter('total_qty', array('gt' => '0'));
        if ($warehouseProducts->getSize()) {
            return false;
        }
        return true;
    }

    /**
     * Check if user can take an action on warehouse
     *
     * @param string $action
     * @param object $warehouse
     * @return bool
     */
    public function isAllowAction($action, $warehouse) {
        $user = Mage::getSingleton('vendors/session')->getUser();
        if ($user->getUsername() == $warehouse->getManager())
            return true;
        if ($user->getUsername() == $warehouse->getCreatedBy())
            return true;
        $permission = $this->_getPermission($warehouse->getId());
        if (!$permission)
            return false;
        if (!$permission->getData('can_' . $action))
            return false;
        return true;
    }

    /**
     * Get permission of user on a warehouse
     *
     * @param int $warehouseId
     * @return Magestore_Inventoryplus_Model_Warehouse_Permission
     */
    protected function _getPermission($warehouseId) {
        if (!Mage::registry('invplus_warehouse_permission_' . $warehouseId)) {
            $user = Mage::getSingleton('vendors/session')->getUser();
            $permission = Mage::getModel('vendorsinventory/permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('vendor_id', $user->getId())
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem();
            Mage::register('invplus_warehouse_permission_' . $warehouseId, $permission);
        }
        return Mage::registry('invplus_warehouse_permission_' . $warehouseId);
    }

    public function getAllWarehouseName() {
        $warehouses = array();
        $vendorId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $model = Mage::getModel('inventoryplus/warehouse');
        $collection = $model->getCollection()
                ->addFieldToFilter('vendor_id',$vendorId);
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }
        return $warehouses;
    }
    
    public function getAllWarehouseNameEnable() {
        $warehouses = array();
        $vendorId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $model = Mage::getModel('inventoryplus/warehouse');
        $collection = $model->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('vendor_id',$vendorId);
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }
        return $warehouses;
    }

}
