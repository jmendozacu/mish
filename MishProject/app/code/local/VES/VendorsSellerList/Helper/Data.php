<?php

class VES_VendorsSellerList_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CODE_BEFORE = 'before';
    const CODE_AFTER = 'after';

    public function getProductsLimit() {
        return Mage::getStoreConfig('vendors/sellers_list/products_limit');
    }

    public function getSellersLimit() {
        return Mage::getStoreConfig('vendors/sellers_list/sellers_limit');
    }

    public function getHeaderStaticBlockId() {
        return Mage::getStoreConfig('vendors/sellers_list/header_static_block');
    }

    public function getFooterStaticBlockId() {
        return Mage::getStoreConfig('vendors/sellers_list/footer_static_block');
    }
}