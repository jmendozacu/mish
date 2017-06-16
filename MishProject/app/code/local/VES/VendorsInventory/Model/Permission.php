<?php

class VES_VendorsInventory_Model_Permission extends Mage_Core_Model_Abstract {
    const STATUS_PENDING = 0;
    const STATUS_COMPLETE = 1;

    public function _construct() {
        parent::_construct();
        $this->_init('vendorsinventory/permission');
    }

    public function loadByWarehouseAndVendor($warehouseId, $adminId) {
        $collection = $this->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouseId);
//                ->addFieldToFilter('vendor_id', $adminId);
        if ($collection->getSize()) {
            $this->load($collection->setPageSize(1)->setCurPage(1)->getFirstItem()->getId());
        }
        return $this;
    }

    /**
     * Add new scanned item
     * 
     * @param int $productId
     * @param string $action
     * @param float $qty
     * @return \Magestore_Inventorybarcode_Model_Barcode_Scanitem
     */
    public function addItem($productId, $action, $qty = 1) {
        $currentAdminId = Mage::getSingleton('vendors/session')->getUser()->getUserId();
        $item = Mage::getModel('inventorybarcode/barcode_scanitem')->getCollection()
                ->addFieldToFilter('product_id', $productId)
//                ->addFieldToFilter('vendor_id', $currentAdminId)
                ->addFieldToFilter('action', $action)
                ->addFieldToFilter('is_finished', self::STATUS_PENDING)
                ->getFirstItem();

        $item->setData('product_id', $productId)
//                ->setData('vendor_id', $currentAdminId)
                ->setData('action', $action)
                ->setData('is_finished', self::STATUS_PENDING)
                ->setData('scan_qty', $item->getScanQty() + $qty)
                ->setData('last_scanned_at', now())
                ->save();

        return $item;
    }

    /**
     * Get scanned items
     * 
     * @param array $productIds
     * @param string $action
     */
    public function getItems($productIds, $action) {
        $currentAdminId = Mage::getSingleton('vendors/session')->getUser()->getUserId();
        $items = Mage::getModel('inventorybarcode/barcode_scanitem')->getCollection();
        if (count($productIds)) {
            $items->addFieldToFilter('product_id', array('in' => $productIds));
        }
        $items//->addFieldToFilter('vendor_id', $currentAdminId)
                ->addFieldToFilter('action', $action)
                ->addFieldToFilter('is_finished', self::STATUS_PENDING);

        return $items;
    }

}
