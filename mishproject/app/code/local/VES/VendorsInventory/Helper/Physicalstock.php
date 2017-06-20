<?php

class VES_VendorsInventory_Helper_Physicalstock extends Mage_Core_Helper_Abstract {

    /**
     * check permission
     * @return string
     */
    public function getPhysicalWarehouseByAdmin($warehouse = null) {
//        Mage::log($warehouse);
        $adminId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $warehouseIds = array();
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $adminId)
                ->addFieldToFilter('can_physical', 1);
        if (count($collection) > 0) {
            foreach ($collection as $assignment) {
                $warehouseIds[] = $assignment->getWarehouseId();
            }
            $warehouseCollection = Mage::getModel('inventoryplus/warehouse')->getCollection()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('warehouse_id', array('in' => $warehouseIds));
            return $warehouseCollection;
        } else {
            return false;
        }
    }

    /**
     * check permission
     * @return string
     */
    public function getAdjustWarehouseByAdmin() {
        $vendorId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $warehouseIds = array();
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $vendorId)
                ->addFieldToFilter('can_adjust', 1)
                ->addFieldToFilter('can_physical', 1);
        foreach ($collection as $assignment) {
            $warehouseIds[] = $assignment->getWarehouseId();
        }
        $warehouseCollection = Mage::getModel('inventoryplus/warehouse')->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('warehouse_id', array('in' => $warehouseIds));
        return $warehouseCollection;
    }

    /**
     * get physical label status
     * @return string
     */
    public function getStatusPhysicalLabel($status) {
        $return = $this->__('Pending');
        if ($status == 1) {
            $return = $this->__('Completed');
        } else if ($status == 2) {
            $return = $this->__('Canceled');
        }
        return $return;
    }

    public function importProduct($data) {
        if (count($data)) {
            Mage::getModel('vendors/session')->setData('physicalstocktaking_product_import', $data);
        }
    }

    public function getPhysicalPermission($data) {
        $vendorId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $warehouseIds = array();
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $vendorId)
                ->addFieldToFilter('can_physical', 1)
                ->addFieldToFilter('warehouse_id', $data->getWarehouseId());
        if (count($collection) > 0) {
            return true;
        }
        return false;
    }

}
