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
 * Inventory Supplier Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Purchaseorder_Edit_Tab_Returnorder extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('returnorderGrid');
        $this->setDefaultSort('return_product_warehouse_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setVarNameFilter('returnorder_filter');
    }

    protected function _prepareLayout() {
        $totalProductRecieved = Mage::helper('vendorsinventory/purchaseorder')->getDataByPurchaseOrderId($this->getRequest()->getParam('id'), 'total_products_recieved');
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $purchaseOrder = Mage::getModel('inventorypurchasing/purchaseorder')->load($purchaseOrderId);
        if (($totalProductRecieved > 0) && $this->checkCreateReturn()) {
            $this->setChild('return_order_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData(array(
                                'label' => Mage::helper('inventorypurchasing')->__('Return Order'),
                                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/newreturnorder', array('purchaseorder_id' => $this->getRequest()->getParam('id'), 'warehouse_ids' => $purchaseOrder->getWarehouseId(), 'action' => 'newreturnorder', '_current' => false)) . '\')',
                                'class' => 'add',
                                'style' => 'float:right'
                            ))
            );
        }
        if ($totalProductRecieved > 0 && $this->checkCreateReturnAll()) {
            $this->setChild('return_all_order_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData(array(
                                'label' => Mage::helper('inventorypurchasing')->__('Return All Orders'),
                                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/returnallorder', array('purchaseorder_id' => $this->getRequest()->getParam('id'), 'warehouse_ids' => $purchaseOrder->getWarehouseId(), 'action' => 'newreturnorder', '_current' => false)) . '\')',
                                'class' => 'add',
                                'style' => 'float:right'
                            ))
            );
        }
        $this->setChild('print_receipt_return_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('inventorypurchasing')->__('Print returned Items'),
                            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/printreceiptforreturned', array('purchaseorder_id' => $this->getRequest()->getParam('id'), 'warehouse_ids' => $purchaseOrder->getWarehouseId(), '_current' => false)) . '\')',
//                    'class' => 'add',
                            'style' => 'float:right'
                        ))
        );
        return parent::_prepareLayout();
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */
    protected function _prepareCollection() {
        $purchase_order_id = Mage::app()->getRequest()->getParam('id');
        $collection = Mage::getModel('inventorypurchasing/purchaseorder_returnproductwarehouse')
                        ->getCollection()->addFieldToFilter('purchase_order_id', $purchase_order_id);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('return_product_warehouse_id', array(
            'header' => Mage::helper('inventorypurchasing')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'return_product_warehouse_id',
        ));

        $this->addColumn('returned_on', array(
            'header' => Mage::helper('inventorypurchasing')->__('Return Date'),
            'width' => '150px',
            'type' => 'datetime',
            'index' => 'returned_on',
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('inventorypurchasing')->__('Name'),
            'align' => 'left',
            'index' => 'product_name',
        ));

        $this->addColumn('product_image', array(
            'header' => Mage::helper('catalog')->__('Image'),
            'width' => '90px',
            'renderer' => 'inventoryplus/adminhtml_renderer_productimage',
            'filter' => false,
        ));

        $this->addColumn('warehouse_name', array(
            'header' => Mage::helper('inventorypurchasing')->__('Warehouse'),
            'width' => '80px',
            'index' => 'warehouse_name'
        ));

        $this->addColumn('qty_return', array(
            'header' => Mage::helper('inventorypurchasing')->__('Qty Returned'),
            'width' => '150px',
            'name' => 'qty_return',
            'type' => 'number',
            'index' => 'qty_return'
        ));

        $this->addColumn('create_by', array(
            'header' => Mage::helper('inventorypurchasing')->__('Create by'),
            'name' => 'create_by',
            'width' => '80px',
            'index' => 'created_by'
        ));

        $this->addColumn('reason', array(
            'header' => Mage::helper('inventorypurchasing')->__('Reason(s)'),
            'name' => 'reason',
            'width' => '150px',
            'index' => 'reason'
        ));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventory_Block_Adminhtml_Supplier_Grid
     */

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return null;
    }

    public function getSearchButtonHtml() {
        return parent::getSearchButtonHtml() . $this->getChildHtml('print_receipt_return_button') . $this->getChildHtml('return_order_button') . $this->getChildHtml('return_all_order_button') . $this->getChildHtml('cancel_order_button');
    }

    public function checkCreateReturnAll() {
        $canReturnAll = true;
        $adminId = Mage::getModel('vendors/session')->getUser()->getId();
        if (!$adminId)
            return null;
        $purchaseOrderId = $this->getRequest()->getParam('id');
        if ($purchaseOrderId) {
            $poProductWH = Mage::getResourceModel('inventorypurchasing/purchaseorder_productwarehouse');
            $results = $poProductWH->getItemData($purchaseOrderId);
            if (count($results) > 0) {
                foreach ($results as $result) {
                    if ($result['warehouse_id']) {
                        if (!Mage::helper('vendorsinventory/adjuststock')->getPermission($result['warehouse_id'], 'can_purchase_product')) {
                            $canReturnAll = false;
                        }
                        //check if is enough stock
                        $warehouse_product = Mage::getModel('inventoryplus/warehouse_product')
                                        ->getCollection()
                                        ->addFieldToFilter('product_id', $result['product_id'])
                                        ->addFieldToFilter('warehouse_id', $result['warehouse_id'])
                                        ->setPageSize(1)->setCurPage(1)->getFirstItem();
                        $qty_return_all = $result['qty_received'] - $result['qty_returned'];
                        if ($warehouse_product->getTotalQty() < $qty_return_all) {
                            $canReturnAll = false;
                            break;
                        }
                    }
                }
            }
        }
        return $canReturnAll;
    }

    public function checkCreateReturn() {
        $canReturn = false;
        $adminId = Mage::getModel('vendors/session')->getUser()->getId();
        if (!$adminId)
            return null;
        $purchaseOrderId = $this->getRequest()->getParam('id');
        $purchaseOrder = Mage::getModel('inventorypurchasing/purchaseorder')->load($purchaseOrderId);
        $warehouseIds = $purchaseOrder->getWarehouseId();
        $warehouseIds = explode(',', $warehouseIds);
        $warehouseAssigneds = Mage::getModel('vendorsinventory/permission')->getCollection()
                ->addFieldToFilter('vendor_id', $adminId);
        $warehouseAvailableIds = array();
        foreach ($warehouseAssigneds as $warehouseAssigned) {
            if ($warehouseAssigned->getData('can_purchase_product'))
                $warehouseAvailableIds[] = $warehouseAssigned->getWarehouseId();
        }
        //check if create all product
        $purchaseOrderProducts = Mage::getModel('inventorypurchasing/purchaseorder_productwarehouse')
                ->getCollection()
                ->addFieldToFilter('warehouse_id', array('in' => $warehouseAvailableIds))
                ->addFieldToFilter('purchase_order_id', $purchaseOrderId);
        foreach ($purchaseOrderProducts as $purchaseOrderProduct) {
            $warehouse_id = $purchaseOrderProduct->getWarehouseId();
            $product_id = $purchaseOrderProduct->getProductId();
            $product = Mage::getModel('inventoryplus/warehouse_product')
                            ->getCollection()
                            ->addFieldToFilter('warehouse_id', $warehouse_id)
                            ->addFieldToFilter('product_id', $product_id)
                            ->setPageSize(1)->setCurPage(1)->getFirstItem();
            if ($product->getId()) {
                $product_qty = $product->getTotalQty();
            }
            $check = $purchaseOrderProduct->getQtyReceived() - $purchaseOrderProduct->getQtyReturned();
            if ($check > 0 && isset($product_qty) && $product_qty > 0) {
                $canReturn = true;
                break;
            }
        }
        return $canReturn;
    }

}
