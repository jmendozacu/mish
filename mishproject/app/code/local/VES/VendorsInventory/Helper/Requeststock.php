<?php

class VES_VendorsInventory_Helper_Requeststock extends Mage_Core_Helper_Abstract {

    public function getWarehouseByAdmin() {
        $admin = Mage::getSingleton('vendors/session')->getUser();
        $collection = $this->loadTransferAbleWarehouses($admin);
        $warehouses = array();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }
        return $warehouses;
    }

    public function loadTransferAbleWarehouses($admin) {
        $warehouses = array();        
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $admin->getId())
                ->addFieldToFilter('can_send_request_stock', 1);
        foreach ($collection as $assignment) {
            $warehouses[] = $assignment->getWarehouseId();
        }
        $warehouseCollection = Mage::getModel('inventoryplus/warehouse')->getCollection()
                ->addFieldToFilter('warehouse_id', array('in' => $warehouses))
                ->addFieldToFilter('warehouse_created_by',2)
                ->addFieldToFilter('vendor_id',$admin->getId())
                ->addFieldToFilter('status', 1);
        return $warehouseCollection;
    }

    public function getAllWarehouseRequeststock() {
        $warehouses = array();
        $model = Mage::getModel('inventoryplus/warehouse');
        $vendorId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $collection = $model->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('warehouse_created_by',2)
                ->addFieldToFilter('vendor_id',$vendorId)
                ;
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }
        if (empty($warehouses['others']))
            $warehouses['others'] = 'Others';
        return $warehouses;
    }

    public function checkCancelRequeststock($id) {
        $store = Mage::app()->getStore();
        $days = 24 * 60 * 60 * Mage::getStoreConfig('inventoryplus/transaction/cancel_time', $store->getId());
        $requestStock = Mage::getModel('inventorywarehouse/requeststock')->load($id);
        $createdAt = strtotime($requestStock->getCreatedAt()) + $days;
        $now = strtotime(now("y-m-d"));
        $warehouseId = $requestStock->getWarehouseIdFrom();
        $admin = Mage::getSingleton('vendors/session')->getUser();
        if ($warehouseId && Mage::helper('vendorsinventory/requeststock')->canSendAndRequest($admin->getId(), $warehouseId)) {
            if (($requestStock->getStatus() == 1) && ($createdAt > $now))
                return true;
        }
        return false;
    }

    public function canSendAndRequest($vendorId, $warehouseId) {
        if ($warehouseId) {
            $assignmentCollection = Mage::getModel('vendorsinventory/permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('vendor_id', $adminId);
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

}
