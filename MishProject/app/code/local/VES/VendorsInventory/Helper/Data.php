<?php

class VES_VendorsInventory_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Is Module Enable
     */
    public function moduleEnable() {
        $result = new Varien_Object(array('module_enable' => true));
        Mage::dispatchEvent('ves_vendorsinventory_module_enable', array('result' => $result));
        return $result->getData('module_enable');
    }

    public function canTransfer($adminId, $warehouseId) {
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $adminId)
                ->addFieldToFilter('warehouse_id', $warehouseId)
        ;
        if ($collection->getSize()) {
            if ($collection->setPageSize(1)->setCurPage(1)->getFirstItem()->getCanSendRequestStock() == 1) {
                return true;
            }
        }
        return false;
    }

    public function getWarehouseByAdmin() {
        $adminId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $warehouseIds = array();
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $adminId)
                ->addFieldToFilter('can_purchase_product', 1);
        foreach ($collection as $assignment) {
            $warehouseIds[] = $assignment->getWarehouseId();
        }
        $warehouseCollection = Mage::getModel('inventoryplus/warehouse')->getCollection()
                ->addFieldToFilter('warehouse_id', array('in' => $warehouseIds));
        if (count($warehouseCollection)) {
            return true;
        }
        return false;
    }

}
