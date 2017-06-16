<?php

class VES_VendorsInventory_Helper_Sendstock extends Mage_Core_Helper_Abstract {

    const XML_PATH_SENDSTOCK_EMAIL = 'inventoryplus/transaction/sendstock_email';
    const XML_PATH_WAREHOUSE_EMAIL = 'inventoryplus/transaction/warehouse_email';

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
                ->addFieldToFilter('status', 1);
        return $warehouseCollection;
    }

    public function getAllWarehouseSendstockforDestination(){
        $warehouses = array();
        $vendorId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $model = Mage::getModel('inventoryplus/warehouse');
        $collection = $model->getCollection()
                ->addFieldToFilter('status',1)
                ->addFieldToFilter('warehouse_created_by',2)
                ->addFieldToFilter('vendor_id',$vendorId)
                ;
       
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }
        if(empty($warehouses['others']))
            $warehouses['others']  = 'Others';
        return $warehouses;
    }
    
    public function sendSendstockEmail($warehouse, $stockId, $isSendstock, $stockName) {
        $user = Mage::getModel('vendors/session')->getUser();
        $storeId = Mage::app()->getStore()->getId();
        $template = Mage::getStoreConfig(self::XML_PATH_SENDSTOCK_EMAIL, $storeId);
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $from_name = $user->getVendorId();
        $from_email = $user->getEmail();
        $sender = array('email' => $from_email, 'name' => $from_name);
        $mailTemplate
                ->setTemplateSubject('Stock Notification')
                ->sendTransactional(
                        $template, $sender, $warehouse->getManagerEmail(), $warehouse->getManagerName(), array(
                    'requeststockid' => $stockId,
                    'issendstock' => $isSendstock,
                    'stockName' => $stockName
                        ), $storeId
        );
        $translate->setTranslateInline(true);
    }
    
    public function checkCancelSendstock($id) {
        $store = Mage::app()->getStore();
        $days = 24 * 60 * 60 * Mage::getStoreConfig('inventoryplus/transaction/cancel_time', $store->getId());
        $sendStock = Mage::getModel('inventorywarehouse/sendstock')->load($id);
        $createdAt = strtotime($sendStock->getCreatedAt()) + $days;
        $now = strtotime(now("y-m-d"));
        $warehouseId = $sendStock->getWarehouseIdTo();
        $admin = Mage::getSingleton('vendors/session')->getUser();

        if ($warehouseId && $this->canSendAndRequest($admin->getId(), $warehouseId)) {
            if (($sendStock->getStatus() == 1) && ($createdAt > $now)) {
                return true;
            }
        }
        return false;
    }
    
    public function canSendAndRequest($adminId, $warehouseId) {
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
