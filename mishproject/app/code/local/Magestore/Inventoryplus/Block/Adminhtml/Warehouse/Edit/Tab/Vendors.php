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
 * Warehouse Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventoryplus_Block_Adminhtml_Warehouse_Edit_Tab_Vendors extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_disable = array();

    public function __construct() {
        parent::__construct();
        $this->setId('vendorGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true); // Using ajax grid is important
        $this->setPagerVisibility(false);
        $this->setDefaultLimit(1000);
    }

    public function getWarehouse() {
        return Mage::getModel('inventoryplus/warehouse')
                        ->load($this->getRequest()->getParam('id'));
    }

    protected function _prepareCollection() {
        $warehouse = $this->getWarehouse();
        $collection = Mage::getModel('vendors/vendor')->getCollection()
                ->addFieldToFilter('status', 1);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
//        $admin = Mage::getModel('vendors/vendor')->getData();
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('inventoryplus')->__('Vendor ID'),
            'sortable' => true,
            'index' => 'entity_id',
            'type' => 'number',
        ));

        $this->addColumn('vendor_id', array(
            'header' => Mage::helper('inventoryplus')->__('Vendor Name'),
            'sortable' => true,
            'index' => 'vendor_id'
        ));

        $this->addColumn('can_adjust', array(
            'header' => Mage::helper('inventoryplus')->__('Adjust Stock'),
            'sortable' => false,
            'filter' => false,
            'width' => '60px',
            'type' => 'checkbox',
            'index' => 'entity_id',
            'align' => 'center',
            'field_name' => 'vendor_adjust[]',
//            'disabled_values' => $this->_disable,
            'values' => $this->_getSelectedCanAdjustAdmins()
        ));

        $this->addColumn('can_purchase_product', array(
            'header' => Mage::helper('inventorypurchasing')->__('Purchase Stock'),
            'sortable' => false,
            'filter' => false,
            'width' => '60px',
            'type' => 'checkbox',
            'index' => 'entity_id',
            'align' => 'center',
//            'disabled_values' => $disabledvalue,
            'field_name' => 'vendor_purchase[]',
            'values' => $this->_getSelectedCanPurchaseAdmins()
        ));

        $this->addColumn('can_physical', array(
            'header' => Mage::helper('inventoryphysicalstocktaking')->__('Physical Stocktaking'),
            'sortable' => false,
            'filter' => false,
            'width' => '60px',
            'type' => 'checkbox',
            'index' => 'entity_id',
            'align' => 'center',
//            'disabled_values' => $disabledvalue,
            'field_name' => 'vendor_physical[]',
            'values' => $this->_getSelectedCanPhysicalAdmins()
        ));

        $this->addColumn('can_send_request_stock', array(
            'header' => Mage::helper('inventorywarehouse')->__('Send/Request Stock'),
            'sortable' => false,
            'filter' => false,
            'width' => '60px',
            'type' => 'checkbox',
            'index' => 'entity_id',
            'align' => 'center',
//            'disabled_values' => $disabledvalue,
            'field_name' => 'vendor_transfer[]',
            'values' => $this->_getSelectedCanTransferAdmins()
        ));

//        Mage::dispatchEvent('inventory_adminhtml_add_column_permission_grid', array('grid' => $this, 'disabled' => $this->_disable,'vendor'=>));

        return parent::_prepareColumns();
    }

    protected function _getSelectedCanAdjustAdmins() {
        $warehouse = $this->getWarehouse();
        $array = array();
        if ($warehouse->getId())
            $canEditAdmins = Mage::getModel('vendorsinventory/permission')->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->addFieldToFilter('can_adjust', 1);
        foreach ($canEditAdmins as $canAdjustAdmin) {
            $array[] = $canAdjustAdmin->getVendorId();
        }
        return $array;
    }

    protected function _getSelectedCanPurchaseAdmins() {
        $warehouse = $this->getWarehouse();
        $array = array();
        if ($warehouse->getId()) {
            $canPurchaseAdmins = Mage::getModel('vendorsinventory/permission')->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->addFieldToFilter('can_purchase_product', 1);
            foreach ($canPurchaseAdmins as $canPurchaseAdmin) {
                $array[] = $canPurchaseAdmin->getVendorId();
            }
        }
        return $array;
    }

    protected function _getSelectedCanPhysicalAdmins() {
        $warehouse = $this->getWarehouse();
        $array = array();
        if ($warehouse->getId()) {
            $canPhysicalAdmins = Mage::getModel('vendorsinventory/permission')->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->addFieldToFilter('can_physical', 1);
            foreach ($canPhysicalAdmins as $canPhysicalAdmin) {
                $array[] = $canPhysicalAdmin->getVendorId();
            }
        }
        return $array;
    }

    protected function _getSelectedCanTransferAdmins() {
        $warehouse = $this->getWarehouse();
        $array = array();
        if ($warehouse->getId()) {
            $canSendRequestAdmins = Mage::getModel('vendorsinventory/permission')->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->addFieldToFilter('can_send_request_stock', 1);
            foreach ($canSendRequestAdmins as $canPhysicalAdmin) {
                $array[] = $canPhysicalAdmin->getVendorId();
            }
        }
        return $array;
    }

    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/vendorGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    public function getRowUrl($row) {
        return null;
    }

}
