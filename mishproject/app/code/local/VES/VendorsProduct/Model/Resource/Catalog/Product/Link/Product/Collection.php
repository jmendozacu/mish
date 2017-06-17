<?php

class VES_VendorsProduct_Model_Resource_Catalog_Product_Link_Product_Collection extends VES_VendorsProduct_Model_Resource_Catalog_Product_Link_Product_Collection_Amasty_Pure {
	/**
     * Retrieve is flat enabled flag
     * Return always false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        // Flat Data can be used only on frontend
        if (Mage::app()->getRequest()->getModuleName()=='vendors') {
            return false;
        }
        
        return parent::isEnabledFlat();
    }
}