<?php

class VES_VendorsInventory_Inventory_StockController extends VES_Vendors_Controller_Action {

    public function indexAction() {
        $this->loadLayout()
                ->_setActiveMenu('inventory')->_title($this->__('Inventory'))->_title($this->__('Stock'))
                ->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Inventory'), Mage::helper('vendorsinventory')->__('Inventory'));
        //->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Withdrawal'), Mage::helper('vendorsinventory')->__('Withdrawal'));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
