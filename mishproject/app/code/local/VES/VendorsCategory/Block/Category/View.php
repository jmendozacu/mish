<?php

/**
 * template vendorscategory/view.phtml
 * @author win7
 *
 */
class VES_VendorsCategory_Block_Category_View extends Mage_Core_Block_Template {

    protected function _prepareLayout() {
        parent::_prepareLayout();

        $this->getLayout()->createBlock('vendorscategory/breadcrumbs');

        return $this;
    }

    public function getProductListHtml() {
        return $this->getChildHtml('product_list');
    }

    /**
     * Retrieve current category model object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory() {
        if (!$this->hasData('current_vendor_category')) {
            $this->setData('current_vendor_category', Mage::registry('current_vendor_category'));
        }
        return $this->getData('current_vendor_category');
    }

    public function getCategory() {
        return $this->getCurrentCategory();
    }

    /**
     * 
     * Can show products list.
     */
    public function canShowProducts() {
        return !$this->getCategory()->getData('is_hide_product');
    }

}
