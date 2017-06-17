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
 * Warehouse Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Warehouse extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'warehouse';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Manage Warehouses');
        $this->_addButtonLabel = Mage::helper('vendorsinventory')->__('Add Warehouse');
        parent::__construct();
        //check admin role/rule
//        $allowAll = false;
        $vendorSession = Mage::getSingleton('vendors/session');  
        $vendorId = $vendorSession->getId();
        $vendorGroup = $vendorSession->getUser()->getGroupId();
        $vendorConfigData = Mage::getModel('vendorsgroup/rule')->getCollection()
                ->addFieldToFilter('group_id', $vendorGroup)
                ->addFieldToFilter('resource_id', 'inventory/max_warehouse');
        $data = $vendorConfigData->getData();
        $maxWarehouse = $data[0]['value'];
        
        $col = Mage::getModel('inventoryplus/warehouse')->getCollection()
                ->addFieldToFilter('vendor_id',$vendorId)
                ->addFieldToFilter('warehouse_created_by',2);
        $total = count($col->getData());
//        print_r($col->getData());
        if($maxWarehouse<=$total){
            $this->_removeButton('add');
        }
//        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
//        $admin = Mage::getModel('admin/user')->load($adminId);
//        $roleId = $admin->getRole()->getId();
//        $adminRule = Mage::getModel('admin/rules')->getCollection()
//                ->addFieldToFilter('role_id', $roleId)
//                ->addFieldToFilter('resource_id', 'all')
//                ->setPageSize(1)->setCurPage(1)
//                ->getFirstItem();
//        if ($adminRule->getPermission() == 'allow') {
//            $allowAll = true;
//        }
//        //remove button if admin does not have permission
//        if (!$allowAll) {
//            $this->_removeButton('add');
//        }
    }

    public function getWarehouseHistory($id) {
        return Mage::getModel('inventoryplus/warehousehistory')->load($id);
    }

    public function getWarehoueContentByHistoryId($id) {
        $collection = Mage::getModel('inventoryplus/warehousehistorycontent')
                ->getCollection()
                ->addFieldToFilter('warehouse_history_id', $id);
        return $collection;
    }

}
