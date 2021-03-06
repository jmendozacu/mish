<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Supplier Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Purchaseorder extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'purchaseorder';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Manage Purchase Orders');
        $this->_addButtonLabel = Mage::helper('vendorsinventory')->__('Create Purchase Order');

        $this->_addButton('Trash', array(
            'label' => Mage::helper('vendorsinventory')->__('Deleted Purchase Orders'),
            'onclick' => "setLocation('{$this->getUrl('*/*/trash')}')",
            'class' => 'delete',
        ), 0);
        parent::__construct();
        if(!Mage::helper('vendorsinventory')->getWarehouseByAdmin()){
            $this->_removeButton('add');
        }
    }
    
    
    public function getWarehouseIdsForPurchase() {
        $warehouseIds = $this->getRequest()->getParam('warehouse_ids', null);
        if (!$warehouseIds) {
            $warehouseIds = Mage::getModel('inventorypurchasing/purchaseorder')->load($this->getRequest()->getParam('id'))->getWarehouseId();
        }
        $warehouseIds = explode(',', $warehouseIds);
        return $warehouseIds;
    }

    public function getWarehouseNameById($warehouseId) {
        return Mage::getModel('inventoryplus/warehouse')->load($warehouseId)->getWarehouseName();
    }

    public function getWarehouseList() {
        $warehouseIds = $this->getWarehouseIdsForPurchase();
        $numberWarehouses = count($warehouseIds);
        $warehouseNames = '';
        $i = 1;
        foreach ($warehouseIds as $warehouseId) {
            if (($numberWarehouses > 1) && ($i > 1)) {
                if ($i == $numberWarehouses) {
                    $warehouseNames .= Mage::helper('inventorypurchasing')->__(' and ');
                } else {
                    $warehouseNames .= ', ';
                }
            }
            $warehouseNames .= '<b>' . $this->getWarehouseNameById($warehouseId) . '</b>';
            $i++;
        }
        return $warehouseNames;
    }

    public function getPurhcaseOrderHistory($id) {
        return Mage::getModel('inventorypurchasing/purchaseorder_history')->load($id);
    }

    public function getPurchaseOrderContentByHistoryId($id) {
        $collection = Mage::getModel('inventorypurchasing/purchaseorder_historycontent')
                ->getCollection()
                ->addFieldToFilter('purchase_order_history_id', $id);
        return $collection;
    }
    
    /**
     * 
     * @return bool
     */
    public function isActiveSupplyNeedsPlugin(){
        return Mage::helper('core')->isModuleEnabled('Magestore_Inventorysupplyneeds');
    }

    /**
     * 
     * @return \Magestore_Inventorysupplyneeds_Model_Draftpo
     */
    public function getDraftPO(){
        if(!$this->hasData('draft_purchaseorder')){
            $draftPO = $this->helper('inventorysupplyneeds')->getDraftPO();
            $this->setData('draft_purchaseorder', $draftPO);
        }
        return $this->getData('draft_purchaseorder');
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasDraftPO(){
        if($this->getDraftPO()->getId())
            return true;
        return false;
    }
    
    /**
     * 
     * @return string
     */
    public function getDraftPOUrl() {
        if(!$this->hasDraftPO())
            return null;
        return $this->getUrl('vendors/inventory_supplyneeds/viewpo', array('id'=>$this->getDraftPO()->getId()));
    }
}