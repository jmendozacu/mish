<?php

class VES_VendorsInventory_Block_Warehouse_Edit_Tab_Permission extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_disable = array();

    public function __construct() {
        parent::__construct();
        $this->setId('permissionGrid');
        $this->setDefaultSort('user_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true); // Using ajax grid is important
        $this->setPagerVisibility(false);
        $this->setDefaultLimit(1000);
    }

    protected function _getSelectedAssignments() {
        $assignments = $this->getAssignments();
        if (!is_array($assignments))
            $assignments = array_keys($this->getSelectedAssignments());
        return $assignments;
    }

    public function _getSelectedUsers() {
        return array();
    }

    /**
     * get select assignment of warehouse
     * @return int
     */
    public function getSelectedAssignments() {
        $assignments = array();
        $warehouse = $this->getWarehouse();
        $collection = Mage::getResourceModel('inventoryplus/warehouse_permission_collection')
                ->addFieldToFilter('warehouse_id', $warehouse->getId());
        foreach ($collection as $assignment) {
            $assignments[$assignment->getId()] = array('position' => 0);
        }
        return $assignments;
    }

    public function getWarehouse() {
        return Mage::getModel('inventoryplus/warehouse')
                        ->load($this->getRequest()->getParam('id'));
    }

    protected function _prepareCollection() {
        $admin = Mage::getSingleton('vendors/session')->getUser();
        $warehouse = $this->getWarehouse();
        $collection = Mage::getModel('vendors/vendor')->getCollection();
        $collection->addFieldToFilter('vendor_id', $admin->getVendorId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $admin = Mage::getSingleton('vendors/session')->getUser();
        $warehouse = $this->getWarehouse();
        if ($warehouse->getWarehouseId()) {
            if ($admin->getUsername() != $warehouse->getManager() && $admin->getUsername() != $warehouse->getCreatedBy()) {
                $this->_disable[] = $admin->getId();
            }
        }
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('inventoryplus')->__('Vendor ID'),
            'sortable' => true,
            'index' => 'entity_id',
            'type' => 'number',
        ));

        $this->addColumn('vendor_id', array(
            'header' => Mage::helper('inventoryplus')->__('Vendor Name'),
            'sortable' => true,
            'index' => 'vendor_id',
        ));

        $this->addColumn('can_edit_warehouse', array(
            'header' => Mage::helper('inventoryplus')->__('Edit Warehouse'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'edit[]',
            'align' => 'center',
            'index' => 'entity_id',
            'sortable' => false,
            'filter' => false,
//            'disabled_values' => $this->_disable,
            'values' => $this->_getSelectedCanEditAdmins(),
        ));

        $this->addColumn('can_adjust', array(
            'header' => Mage::helper('inventoryplus')->__('Adjust Stock'),
            'sortable' => false,
            'filter' => false,
            'width' => '60px',
            'type' => 'checkbox',
            'index' => 'entity_id',
            'align' => 'center',
            'field_name' => 'adjust[]',
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

//        Mage::dispatchEvent('inventory_adminhtml_add_column_permission_grid', array('grid' => $this, 'disabled' => $this->_disable));

        return parent::_prepareColumns();
    }

    protected function _getSelectedCanPurchaseAdmins() {
        $warehouse = $this->getWarehouse();
        $array = array();
        if ($warehouse->getId()) {
            $canPurchaseAdmins = Mage::getModel('vendorsinventory/permission')->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->addFieldToFilter('can_purchase_product', 1);
            if ($canPurchaseAdmins) {
                foreach ($canPurchaseAdmins as $canPurchaseAdmin) {
                    $array[] = $canPurchaseAdmin->getVendorId();
                }
            }
        }  else {
            $array[] = Mage::getSingleton('vendors/session')->getUser()->getId();
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
        }  else {
            $array[] = Mage::getSingleton('vendors/session')->getUser()->getId();
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
        }else{
            $array[] = Mage::getSingleton('vendors/session')->getUser()->getId();
        }
        return $array;
    }

    protected function _getSelectedCanEditAdmins() {
        $warehouse = $this->getWarehouse();
        $array = array();
        if ($warehouse->getId()) {
            $canEditAdmins = Mage::getModel('vendorsinventory/permission')->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->addFieldToFilter('can_edit_warehouse', 1);
            //->getAllCanEditAdmins();
            foreach ($canEditAdmins as $canWarehouseAdmin) {
                $array[] = $canWarehouseAdmin->getVendorId();
            }
        }else{
            $array[] = Mage::getSingleton('vendors/session')->getUser()->getId();
        }
        return $array;
    }

    protected function _getSelectedCanAdjustAdmins() {
        $warehouse = $this->getWarehouse();
        $array = array();
        if ($warehouse->getId()) {
            $canEditAdmins = Mage::getModel('vendorsinventory/permission')->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse->getId())
                    ->addFieldToFilter('can_adjust', 1);
//                    ->getAllCanAdjustAdmins();
            foreach ($canEditAdmins as $canAdjustAdmin) {
                $array[] = $canAdjustAdmin->getVendorId();
            }
        }else{
            $array[] = Mage::getSingleton('vendors/session')->getUser()->getId();
        }
        return $array;
    }

    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/permissionGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    public function getRowUrl($row) {
        return null;
    }

}
