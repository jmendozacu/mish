<?php

class VES_VendorsCategory_Block_Vendor_Category_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('vendor_catalog_category_products');
        $this->setNameInLayout('vendor.category.product.grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    public function getVendor() {
        return Mage::getSingleton('vendors/session')->getVendor();
    }

    public function getCategory()
    {
        return Mage::registry('category');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('category_ids', array('finset'=>2));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_category'=>1));
        }

        $collection 	= Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendor()->getId())
            ->addAttributeToFilter('vendor_categories',array('finset'=>$this->getCategory()->getId()))
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        /*$this->addColumn('in_category', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_category',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));*/

        $this->addColumn('grid[entity_id]', array(
            'header'    => Mage::helper('vendors')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('grid[name]', array(
            'header'    => Mage::helper('vendors')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('grid[sku]', array(
            'header'    => Mage::helper('vendors')->__('SKU'),
            'width'     => '80',
            'index'     => 'vendor_sku'
        ));
        $this->addColumn('grid[price]', array(
            'header'    => Mage::helper('vendors')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

/*
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if (is_null($products)) {
            $products = $this->getCategory()->getProductIds();
            return array_keys($products);
        }
        return $products;
    }*/
}