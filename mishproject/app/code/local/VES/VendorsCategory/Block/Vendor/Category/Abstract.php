<?php

class VES_VendorsCategory_Block_Vendor_Category_Abstract extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retrieve current category instance
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('category');
    }

    public function getCategoryData() {
        return $this->getCategory()->getData();
    }

    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
    }

    public function getCategoryName()
    {
        return $this->getCategory()->getName();
    }

    public function getStore()
    {
        return Mage::app()->getStore(Mage::app()->getStore()->getId());
    }

    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    public function getEditUrl()
    {
        return $this->getUrl("*/*/edit", array('_current'=>true, 'store'=>null, '_query'=>false, 'id'=>null, 'parent'=>null));
    }
}
