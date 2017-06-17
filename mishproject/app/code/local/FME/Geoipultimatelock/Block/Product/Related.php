<?php

class FME_Geoipultimatelock_Block_Product_Related extends Mage_Catalog_Block_Product_List_Related {

    protected function _prepareData() {

        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */
        $excludeId = $this->_geoipUltimate($product->getRelatedProductCollection());

        $this->_itemCollection = $product->getRelatedProductCollection()
                ->addAttributeToSelect('required_options')
                ->setPositionOrder()
                ->addStoreFilter();

        if (count($excludeId) > 0) {
            $this->_itemCollection->addFieldToFilter('entity_id', array('nin' => $excludeId));
        }

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection, Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    protected function _geoipUltimate($coll) {

        $_helper = Mage::helper('geoipultimatelock');
        $_blockProductIds = array();
        if (!$_helper->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }

        $currentIp = Mage::helper('core/http')->getRemoteAddr(); //echo $currentIp;// this will get the visitor ip address
        //$currentIp = '58.65.183.10';//'41.99.121.142'; //'58.65.183.10'; // testing purpose
        //$_helper->blockBlackList($currentIp);
        $currentStore = Mage::app()->getStore()->getId();
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = $_helper->getInfoByIp($currentIp); //print_r($infoByIp);exit; // get ip related info for country code and country name by remote id if exists
        if (empty($infoByIp)) {
            return;
        }

        $country = $infoByIp['cn']; // country name
        /* country based, store based collection of rules */
        $_geoipCollection = $_helper->getRulesByCountry($country, $currentStore, false, false);
        if (empty($_geoipCollection)) {
            return;
        }
        $prodData = $coll->getData();
        $geoipData = $_geoipCollection->getData();

        return $_helper->getBlockedProductIds($prodData, $geoipData, $currentIp);
    }

    public function isEnabled($storeId = null) {
        return Mage::getStoreConfig('geoipultimatelock/main/enable', $storeId);
    }

}
