<?php

class VES_VendorsInventory_Inventory_OutofstockController extends VES_Vendors_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorylowstock_Adminhtml_NotificationlogController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('vendors/inventory_outofstock/')
                ->_addBreadcrumb(
                        Mage::helper('vendorsinventory')->__('Out Of Stock Tracking'), Mage::helper('vendorsinventory')->__('Out Of Stock Tracking')
        );
        $this->_title($this->__('Inventory'))
                ->_title($this->__('Out Of Stock Tracking'));
        return $this;
    }

    public function insertAction() {
        $listOosProducts = Mage::getResourceModel('inventorylowstock/notificationlog_collection')->sqlcollection();
        $insertSql = '';
        $countSql = 0;
        foreach ($listOosProducts as $product) {
            $insertSql .= ' INSERT INTO ' . $coreResource->getTableName('erp_inventory_outofstock_tracking')
                    . ' (`product_id`, `outofstock_date`)'
                    . " VALUES ('" . $product->getId() . "', '" . $product->getOutofstockAt() . "');";

            $countSql++;
            if ($countSql == 900) {
                $writeConnection->query($insertSql);
                $insertSql = '';
                $countSql = 0;
            }
        }
        if (!empty($insertSql)) {
            $writeConnection->query($insertSql);
        }
    }



    protected function _isAllowed() {
        return true;//Mage::getSingleton('admin/session')->isAllowed('inventorylowstock');
    }

}
