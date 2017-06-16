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
 * Inventory Helper
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventoryplus_Helper_Warehouse extends Mage_Core_Helper_Abstract {

    public function getCountryList() {
        $result = array();
        $collection = Mage::getModel('directory/country')->getCollection();
        foreach ($collection as $country) {
            $cid = $country->getId();
            $cname = $country->getName();
            $result[$cid] = $cname;
        }
        return $result;
    }

    public function getCountryListHash() {
        $options = array();
        foreach ($this->getCountryList() as $value => $label) {
            if ($label)
                $options[] = array(
                    'value' => $value,
                    'label' => $label
                );
        }
        return $options;
    }

    /**
     * Check if user can take an action on warehouse
     *
     * @param string $action
     * @param object $warehouse
     * @return bool
     */
    public function isAllowAction($action, $warehouse) {
        $user = Mage::getSingleton('admin/session')->getUser();
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
            $user = Mage::getSingleton('admin/session')->getUser();
            $permission = Mage::getModel('inventoryplus/warehouse_permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('admin_id', $user->getId())
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem();
            Mage::register('invplus_warehouse_permission_' . $warehouseId, $permission);
        }
        return Mage::registry('invplus_warehouse_permission_' . $warehouseId);
    }

    public function canAdjust($adminId, $warehouseId) {
        if ($warehouseId) {
            $assignmentCollection = Mage::getModel('inventoryplus/warehouse_permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('admin_id', $adminId);
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

    public function canSendAndRequest($adminId, $warehouseId) {
        if ($warehouseId) {
            $assignmentCollection = Mage::getModel('inventoryplus/warehouse_permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('admin_id', $adminId);
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

    public function canEdit($adminId, $warehouseId) {
        if ($warehouseId) {
            $assignmentCollection = Mage::getModel('inventoryplus/warehouse_permission')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('admin_id', $adminId);
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

    public function getProductSkuByProductId($productId) {
        $product = Mage::getResourceModel('catalog/product_collection')
                            ->addFieldToFilter('entity_id', $productId)
                            ->setPageSize(1)->setCurPage(1)->getFirstItem();
        return $product->getSku();
    }

    public function deleteWarehouseProducts($warehouse, $list) {
        $warehouseProducts = Mage::getModel('inventoryplus/warehouse_product')
                ->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouse->getId())
                ->addFieldToFilter('product_id', array('nin' => $list));
        $warehouseProductSkus = '';
        if ($warehouseProducts->getSize()) {
            $i = 0;
            foreach ($warehouseProducts as $wp) {
                if ($i != 0)
                    $warehouseProductSkus .= ', ';
                $warehouseProductSkus .= $this->getProductSkuByProductId($wp->getId());
                if ($wp->getTotalQty() == 0)
                    $wp->delete();
            }
        }
        return $warehouseProductSkus;
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

    public function getAllWarehouseName() {
        $warehouses = array();
        $model = Mage::getModel('inventoryplus/warehouse');
        $collection = $model->getCollection();
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }
        return $warehouses;
    }

    public function getSelectedStores() {
        $stores = Mage::getModel('core/store_group')->getCollection();
        return $stores;
    }

    //get all warehouse can edit
    public function getWarehouseEnable() {
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $warehouseIds = array();
        $warehousePermission = Mage::getModel('inventoryplus/warehouse_permission')
                ->getCollection()
                ->addFieldToFilter('admin_id', $adminId)
                ->addFieldToFilter('can_edit_warehouse', 1);
        foreach ($warehousePermission as $warehouse)
            $warehouseIds[] = $warehouse->getWarehouseId();
        return $warehouseIds;
    }

    //get warehouse by product id
    public function getWarehouseByProductId($productId, $checkqtyZero = true) {
        if ($checkqtyZero == true) {
            $warehouseProducts = Mage::getModel('inventoryplus/warehouse_product')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('total_qty', array('gt' => 0));
        } else {
            $warehouseProducts = Mage::getModel('inventoryplus/warehouse_product')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $productId);
        }
        if (count($warehouseProducts)) {
            return $warehouseProducts;
        } else {
            return null;
        }
    }

    //get warehouse by product id
    public function getWarehouseStockByProductId($productId, $checkqtyZero = true) {
        $list = array();
        $warehouseProducts = Mage::getModel('inventoryplus/warehouse_product')
                ->getCollection()
                ->addFieldToFilter('product_id', $productId);
        if ($checkqtyZero) {
            $warehouseProducts->addFieldToFilter('total_qty', array('gt' => 0));
        }

        if (count($warehouseProducts)) {
            foreach ($warehouseProducts as $warehouseProduct) {
                $list[$warehouseProduct->getWarehouseId()] = $warehouseProduct->getData();
            }
        }
        return $list;
    }

    public function checkWarehouseAvailableProduct($warehouseId, $productId, $qty) {
        $warehouseProductModel = Mage::getModel('inventoryplus/warehouse_product')->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouseId)
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('total_qty', array('gteq' => $qty));
        if ($warehouseProductModel->setPageSize(1)->setCurPage(1)->getFirstItem()->getData()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * get the firrst warehouse ha most this product
     */

    public function getFirstWarehouseHaveMostOfAProduct($productId) {

        $warehouseProductModel = Mage::getModel('inventoryplus/warehouse_product')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('total_qty', 'DESC');
        if (count($warehouseProductModel) > 1) {
            $warehouseProductModel = Mage::getModel('inventoryplus/warehouse_product')->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->setOrder('total_qty', 'DESC');
        }
        if ($warehouseProductModel->setPageSize(1)->setCurPage(1)->getFirstItem()->getId()) {
            $warehouseId = $warehouseProductModel->setPageSize(1)->setCurPage(1)->getFirstItem()->getWarehouseId();
        } else {
            $allWarehouse = $this->getAllWarehouseNameEnable();
            foreach ($allWarehouse as $warehouseIdKey => $warehouseNameValue) {
                if ($warehouseNameValue != '') {
                    $warehouseId = $warehouseIdKey;
                    break;
                }
            }
        }
        return $warehouseId;
    }

    public function checkTheFirstWarehouseAvailableProduct($productId, $minQty, $orderId) {
        $minQty++;
        $warehouseOrder = Mage::getModel('inventoryplus/warehouse_order')->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('product_id', $productId);
        $firstWarehouse = $warehouseOrder->setPageSize(1)->setCurPage(1)->getFirstItem()->getWarehouseId();

        $warehouseProductModel = Mage::getModel('inventoryplus/warehouse_product')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('warehouse_id', $firstWarehouse)
                ->addFieldToFilter('total_qty', array('gteq' => $minQty))
                ->setOrder('total_qty', 'DESC');

        if ($warehouseProductModel->setPageSize(1)->setCurPage(1)->getFirstItem()->getData()) {
            return true;
        } else {
            return false;
        }
    }

    public function selectboxWarehouseShipmentByPid($productId, $minQty, $orderItemId, $orderId = null) {
        $minQty++;
        $warehouseOrder = Mage::getModel('inventoryplus/warehouse_order')->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('product_id', $productId);

        $allWarehouse = $this->getAllWarehouseNameEnable();

        // If order is created from M2ePro module, only display warehouse
        $isM2eProActive = Mage::helper('inventoryplus/integration')->isM2eProActive();
        if ($isM2eProActive) {
            $isOrderFromM2ePro = Mage::helper('inventorym2epro/warehouse')->isOrderFromM2ePro($orderId);
            $warehouseId = Mage::helper('inventorym2epro/warehouse')->getWarehouseForM2ePro();
            if ($isOrderFromM2ePro && $warehouseId) {
                $warehouseProductModel = Mage::getResourceModel('inventoryplus/warehouse_product_collection')
                    ->addFieldToFilter('warehouse_id', $warehouseId)
                    ->addFieldToFilter('product_id', $productId);
            } else {
                $warehouseProductModel = Mage::getResourceModel('inventoryplus/warehouse_product_collection')
                    ->addFieldToFilter('product_id', $productId)
                    ->setOrder('total_qty', 'DESC');
            }
        } else {
            $warehouseProductModel = Mage::getResourceModel('inventoryplus/warehouse_product_collection')
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('total_qty', 'DESC');
        }
        $warehouseHaveProduct = array();
        $return = "<select class='warehouse-shipment' name='warehouse-shipment[items][$orderItemId]' onchange='changeviewwarehouse(this,$orderItemId);' id='warehouse-shipment[items][$orderItemId]'>";
        $firstWarehouse = $warehouseOrder->setPageSize(1)->setCurPage(1)->getFirstItem()->getWarehouseId();
        foreach ($warehouseProductModel as $model) {
            $warehouseId = $model->getWarehouseId();
            if (!isset($allWarehouse[$warehouseId]))
                continue;
            $warehouseName = $allWarehouse[$warehouseId];
            $productQty = 0 + $model->getTotalQty();
            if ($warehouseName != '') {
                if (!$firstWarehouse)
                    $firstWarehouse = $warehouseId;
                $return .= "<option value='$warehouseId' ";
                if ($warehouseId == $firstWarehouse) {
                    $return .= ' selected';
                }
                $return .= ">$warehouseName (" . ($productQty + 0) . ")</option>";
                $warehouseHaveProduct[] = $allWarehouse[$warehouseId];
            }
        }

        $return .= "</select><br />";
        $return .= "<div style='float:right;'><a id='view_warehouse-shipment[items][$orderItemId]' target='_blank' href='" . Mage::getBlockSingleton('inventoryplus/adminhtml_warehouse')->getUrl('adminhtml/inp_warehouse/edit') . 'id/' . $firstWarehouse . "'>" . $this->__('view') . "</a></div>";
        return $return;
    }

    public function getAllWarehouseNameEnable() {
        $warehouses = array();
        $model = Mage::getModel('inventoryplus/warehouse');
        $collection = $model->getCollection()
                ->addFieldToFilter('status', 1);
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }
        return $warehouses;
    }

    /*
     * get warehouse name by warehouse id in model inventory/warehouse
     */

    public function getWarehouseNameByWarehouseId($warehouseId) {
        $warehouseModel = Mage::getModel('inventoryplus/warehouse')->load($warehouseId);
        $warehouseName = $warehouseModel->getWarehouseName();
        return $warehouseName;
    }

    public function getOnHoldQty($product_id, $warehouse_id) {
        return Mage::getResourceModel('inventoryplus/warehouse_order')->getOnHoldQty($product_id, $warehouse_id);
    }

    /**
     * Get total qty of product(s) in warehouse
     * 
     * @param string|int|array $productId
     * @param type $warehouseId
     */
    public function getTotalQty($productId, $warehouseId) {
        $totalQty = 0;
        $collection = Mage::getResourceModel('inventoryplus/warehouse_product_collection')
                ->addFieldToFilter('warehouse_id', $warehouseId);
        if (is_array($productId)) {
            $collection->addFieldToFilter('product_id', array('in' => $productId));
        } else {
            $collection->addFieldToFilter('product_id', $productId);
        }
        if (count($collection)) {
            foreach ($collection as $item) {
                $totalQty += $item->getTotalQty();
            }
        }
        return $totalQty;
    }

    public function getPrimaryWarehouse() {
        $warehouse = Mage::getModel('inventoryplus/warehouse')->getCollection()
                ->addFieldToFilter('is_root', 1)
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();
        return $warehouse;
    }

    public function selectboxWarehouseShipmentByPidAndWarehouseId($productId, $minQty, $orderItemId, $orderId = null, $warehouseWarehouseId) {
        $minQty++;
        $warehouseOrder = Mage::getModel('inventoryplus/warehouse_order')->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('product_id', $productId);

        $allWarehouse = $this->getAllWarehouseNameEnable();
        $warehouseProductModel = Mage::getModel('inventoryplus/warehouse_product')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('total_qty', 'DESC');
        $warehouseHaveProduct = array();
        $return = "<select class='warehouse-shipment' name='warehouse-shipment[items][$orderItemId]' onchange='changeviewwarehouse(this,$orderItemId);' id='warehouse-shipment[items][$orderItemId]'>";
        $firstWarehouse = $warehouseOrder->setPageSize(1)->setCurPage(1)->getFirstItem()->getWarehouseId();
        foreach ($warehouseProductModel as $model) {
            $warehouseId = $model->getWarehouseId();
            if (!isset($allWarehouse[$warehouseId]))
                continue;
            $warehouseName = $allWarehouse[$warehouseId];
            $productQty = $model->getTotalQty();
            if ($warehouseName != '') {
                if (!$firstWarehouse)
                    $firstWarehouse = $warehouseId;
                $return .= "<option value='$warehouseId' ";
                if ($warehouseId == $warehouseWarehouseId) {
                    $return .= ' selected';
                }
                $return .= ">$warehouseName($productQty product(s))</option>";
                $warehouseHaveProduct[] = $allWarehouse[$warehouseId];
            }
        }
        foreach ($allWarehouse as $warehouseIdKey => $warehouseNameValue) {
            if ($warehouseNameValue != '') {
                if (in_array($allWarehouse[$warehouseIdKey], $warehouseHaveProduct) == false) {
                    if (!$firstWarehouse)
                        $firstWarehouse = $warehouseIdKey;
                    $return .= "<option value='$warehouseIdKey' ";
                    $return .= ">$warehouseNameValue(0 product(s))</option>";
                }
            }
        }

        $return .= "</select><br />";
        $return .= "<div style='float:right;'><a id='view_warehouse-shipment[items][$orderItemId]' target='_blank' href='" . Mage::getBlockSingleton('inventoryplus/adminhtml_warehouse')->getUrl('adminhtml/inp_warehouse/edit') . 'id/' . $firstWarehouse . "'>" . $this->__('view') . "</a></div>";
        return $return;
    }

    public function getProductLocation($warehouseId,$productId){
        $model= Mage::getModel('inventoryplus/warehouse_product')
                ->getCollection()
                ->addFieldToFilter('warehouse_id', $warehouseId)
                ->addFieldToFilter('product_id', $productId)
                ->setPageSize(1)
                ->setCurPage(1)
		->getFirstItem();
        $location = $model->getProductLocation();
        return $location;
    }
}
