<?php

class VES_VendorsInventory_Block_Stock_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $vendorSession = Mage::getSingleton('vendors/session');
        $vendorId = $vendorSession->getVendorId();
        $vendorGroup = $vendorSession->getUser()->getGroupId();
        $vendorConfigData = Mage::getModel('vendorsgroup/rule')->getCollection()
                ->addFieldToFilter('group_id', $vendorGroup)
                ->addFieldToFilter('resource_id', 'product/product_limit');
        $data = $vendorConfigData->getData();
        $productLimit = $data[0]['value'];

        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('vendor_id', $vendorId);
//                ->addAttributeToSelect('sku')
//                ->addAttributeToSelect('name')
//                ->addAttributeToSelect('attribute_set_id')
//                ->addAttributeToSelect('type_id');
//$resource = Mage::getSingleton('core/resource');
//$collection->join(array('warehouse' => $resource->getTableName("erp_inventory_warehouse")), "main_table.warehouse_id = warehouse.warehouse_id", array('warehouse.*'));
//        $collection->joinField('warehouse_country',$resource->getTableName("erp_inventory_warehouse"),"warehouse_country", "{{table}}.warehouse_id = warehouse.warehouse_id", 'inner');
        $collection->joinField('is_in_stock', 'cataloginventory/stock_item', 'is_in_stock', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
//        $collection->joinTable(array('vendor_table' => 'vendors/vendor'), 'entity_id = vendor_id', array('vendor' => 'vendor_id'), null, 'left');
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                    'name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore
            );
            $collection->joinAttribute(
                    'custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId()
            );
            $collection->joinAttribute(
                    'status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId()
            );
            $collection->joinAttribute(
                    'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId()
            );
            $collection->joinAttribute(
                    'price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId()
            );
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $collection->addAttributeToSort('entity_id', 'DESC');
        $collection->clear()->setPageSize($productLimit)->load();
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _prepareColumns() {

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'width' => '50px',
            'type' => 'number',
            'index' => 'entity_id',
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('product_image', array(
            'header' => Mage::helper('catalog')->__('Image'),
            'width' => '90px',
            'filter' => false,
            'renderer' => 'inventoryplus/adminhtml_renderer_productimage'
        ));

        $store = $this->_getStore();
        $this->addColumn('price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index' => 'price',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty', array(
                'header' => Mage::helper('catalog')->__('Avail. Qty'),
                'width' => '100px',
                'type' => 'number',
                'index' => 'qty',
//                'renderer'  => VES_VendorsInventory_Block_Stock_Renderer_Qty,
            ));
        }

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('is_in_stock', array(
                'header' => Mage::helper('catalog')->__('Availability'),
                'type' => 'options',
                'options' => array(0 => $this->__('Out of stock'), 1 => $this->__('In stock')),
                'index' => 'is_in_stock',
            ));
        }

        $this->addColumn('visibility', array(
            'header' => Mage::helper('catalog')->__('Visibility'),
            'width' => '70px',
            'index' => 'visibility',
            'type' => 'options',
            'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '70px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

//        $this->addColumn('warehouse_country', array(
//            'header' => Mage::helper('catalog')->__('Warehouse\'s Country'),
//            'width' => '150px',
//            'align' => 'left',
//            'index' => 'country_id',
//            'type' => 'options',
//            'options' => Mage::helper('inventoryplus')->getCountryList()
//        ));
        
         $this->addColumn('updated_at', array(
            'header' => Mage::helper('catalog')->__('Date Updated'),
            'width' => '70px',
            'index' => 'updated_at',
            'renderer' => 'vendorsinventory/stock_renderer_date'
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name', array(
                'header' => Mage::helper('catalog')->__('Name in %s', $store->getName()),
                'index' => 'custom_name',
            ));
        }

//        $this->addColumn('type', array(
//            'header' => Mage::helper('catalog')->__('Type'),
//            'width' => '60px',
//            'index' => 'type_id',
//            'type' => 'options',
//            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
//        ));
//
//        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
//                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
//                ->load()
//                ->toOptionHash();
//
//        $this->addColumn('set_name', array(
//            'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
//            'width' => '100px',
//            'index' => 'attribute_set_id',
//            'type' => 'options',
//            'options' => $sets,
//        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites', array(
                'header' => Mage::helper('catalog')->__('Websites'),
                'width' => '100px',
                'sortable' => false,
                'index' => 'websites',
                'type' => 'options',
                'options' => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('catalog')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')) {
            $this->getMassactionBlock()->addItem('attributes', array(
                'label' => Mage::helper('catalog')->__('Update Attributes'),
                'url' => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current' => true))
            ));
        }

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    public function getRowUrl($row) {
        return false;
//        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
