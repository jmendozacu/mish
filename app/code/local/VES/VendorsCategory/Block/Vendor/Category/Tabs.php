<?php

class VES_VendorsCategory_Block_Vendor_Category_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('catalog')->__('Category Data'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    protected function _prepareLayout()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('vendorscategory')->__('Category Information'),
            'title'     => Mage::helper('vendorscategory')->__('Category Information'),
            'content'   => $this->getLayout()->createBlock('vendorscategory/vendor_category_tab_main')->toHtml(),
        ));
        $this->addTab('meta_section', array(
            'label'     => Mage::helper('vendorscategory')->__('Meta Data'),
            'title'     => Mage::helper('vendorscategory')->__('Meta Data'),
            'content'   => $this->getLayout()->createBlock('vendorscategory/vendor_category_tab_meta')->toHtml(),
        ));

        $this->addTab('product_section', array(
            'label'     => Mage::helper('vendorscategory')->__('Category Products'),
            'title'     => Mage::helper('vendorscategory')->__('Category Products'),
            'content'   => $this->getLayout()->createBlock('vendorscategory/vendor_category_tab_product','vendor.category.product.grid')->toHtml(),
        ));

        return parent::_prepareLayout();
    }
}