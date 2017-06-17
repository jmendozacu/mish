<?php

/**
 * Geoip Ultimate Lock extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Geoipultimatelock
 * @author     RT <rafay.tahir@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
class FME_Geoipultimatelock_Model_Observer {

    public function isEnabled($storeId = null) {
        return Mage::getStoreConfig('geoipultimatelock/main/enable', $storeId);
    }

    protected function _getHelper() {
        return Mage::helper('geoipultimatelock');
    }

    public function checkBlackListedIP(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }
        $currentIp = Mage::helper('core/http')->getRemoteAddr(); // this will get the visitor ip address
        //$currentIp = '180.180.121.177'; // testing purpose
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp);  //echo '<pre>';print_r($infoByIp);echo '</pre>';exit;
        if (empty($infoByIp)) {
            return;
        }
        /* priority 1 for , immidiate site block for blocked ips */
        $this->_getHelper()->blockBlackList($currentIp);
    }

    /**
     * 
     * @param Varien_Event_Observer $observer
     * @return type unknown <extension is disabled, no info by IP, empty collection, current IP is an exception>
     */
    public function geoCheck(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }
        /*
          $isHomepage = Mage::getBlockSingleton('page/html_header')->getIsHomePage();
          if ($isHomepage) {
          return;
          } */
        $currentIp = Mage::helper('core/http')->getRemoteAddr(); // this will get the visitor ip address
        //$currentIp = '180.180.121.177'; // testing purpose
        $infoByIp = Mage::helper('geoipultimatelock')->getInfoByIp($currentIp);  //echo '<pre>';print_r($infoByIp);echo '</pre>';exit;
        if (empty($infoByIp)) {
            return;
        }

        $currentStore = Mage::app()->getStore()->getId(); // current store id
        $currentCmsStore = $observer->getEvent()->getPage()->getStoreId();
        $currentCms = '';
        if (in_array($currentStore, $currentCmsStore) || in_array(0, $currentCmsStore)) {
            $currentCms = $observer->getEvent()->getPage()->getPageId();
        }

        /* priority 1 for , immidiate site block for blocked ips */
        $this->_getHelper()->blockBlackList($currentIp);
        $country = $infoByIp['cn']; // country name
        /* prioritize result when an ip falls in more than 1 acls and limit 1 */
        $_geoipCollection = $this->_getHelper()->getRulesByCountry($country, $currentStore, true, true, $currentCms);
        //echo (string) $_geoipCollection->getSelect();exit;
        if (!$_geoipCollection->count() > 0) {
            return;
        }

        $applyRule = true;

        foreach ($_geoipCollection as $i) {
            //$stores = preg_split('@,@', $i->getStores(), NULL, PREG_SPLIT_NO_EMPTY);
            $ipExcepArr = explode(',', $i->getIpsException());
            /* filter an array for bad ip input */
            $ipFtrArr = $this->_getHelper()->validateIpFilter($ipExcepArr);
            /* if current ip is not an exception, proceed */
            if (in_array($currentIp, $ipFtrArr)) {
                /* first to check if rules are not available */
                return;
            }
            //case 1
            if ($i->getCmsPages() == '' || $i->getCmsPages() == 'NULL') {
                if ($i->getRules() == '') {
                    $applyRule = true;
                }
                
                $applyRule = false;
            }
            //case 2
            $_pagesArr = preg_split('@,@', $i->getCmsPages(), NULL, PREG_SPLIT_NO_EMPTY);
            //case 3
            if (!in_array($currentCms, $_pagesArr) && $i->getRules() != '') {
                $applyRule = false;
            } else {
                $applyRule = true;
            }
            /*
             * if (empty($_pagesArr)) {
				if ($i->getRules() != ''){
					$applyRule = false;
				} else {
					$applyRule = true;
				}
			}
			
			if (in_array($currentCms, $_pagesArr)) {
				$applyRule = true;
			} elseif (!in_array($currentCms, $_pagesArr) && $i->getRules() != '') {
				$applyRule = false;
			} else {
				$applyRule = true;
			}
             */
            if ($applyRule) {
                /* redirect or show blank if store is given */
                $this->_getHelper()->redirectOrMessage($i->getRedirectUrl(), $i->getNotes());
            }
        }
    }

    /**
     * 
     * @param Varien_Event_Observer $observer
     * @return type unknown <extension is disabled, info by IP is empty, no product id to block>
     */
    public function filterByGeoip(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }
        $currentIp = Mage::helper('core/http')->getRemoteAddr(); //echo $currentIp;// this will get the visitor ip address
        /* priority 1 for , immidiate site block for blocked ips */
        $this->_getHelper()->blockBlackList($currentIp);
        //$currentIp = '80.180.121.177';//'58.65.183.10';//'41.99.121.142'; //'58.65.183.10'; // testing purpose
        $currentStore = Mage::app()->getStore()->getId(); // current store id
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = $this->_getHelper()->getInfoByIp($currentIp); //print_r($infoByIp);exit; // get ip related info for country code and country name by remote id if exists
        if (empty($infoByIp)) {
            return;
        }
        $country = $infoByIp['cn']; // country name
        /* get rules by country */
        $_geoipCollection = $this->_getHelper()->getRulesByCountry($country, $currentStore, false, false);

        if (!$_geoipCollection->count() > 0) {
            return;
        }
        
        if (!Mage::registry('current_category')) { 
			
            return;
        }
        
        $_currentCategory = Mage::getModel('catalog/category')
                ->load(Mage::registry('current_category')->getId()); 
        $currentPage = (int) Mage::App()->getRequest()->getParam('p');
        $_productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addStoreFilter()
                ->addCategoryFilter($_currentCategory);
        $productData = $_productCollection->getData();
        $geoipData = $_geoipCollection->getData();
        //>setPage($currentPage, 6); //echo $_productCollection->count();
        //current product collection

        $_excludeProducts = $this->_getHelper()
			->getBlockedProductIds($productData, $geoipData, $currentIp);

        if (empty($_excludeProducts)) {
            return;
        }
        /* filtering collection */
        $observer->getEvent()
			->getCollection()
			->addFieldToFilter('entity_id', array('nin' => array_unique($_excludeProducts)));
    }

    /**
     * Saving message in configuration whiswig filtered.
     * @param Varien_Event_Observer $observer
     */
    public function saveFmeContents(Varien_Event_Observer $observer) {

        $coreDb = Mage::getSingleton('core/config');
        if (isset($_POST['fmedescription'])) {
            $coreDb->saveConfig('geoipultimatelock/basics/block_message', stripslashes($_POST['fmedescription']));
        }
    }

    /**
     * Product detail page before product load.
     * @param Varien_Event_Observer $observer product data before product load
     * @return type unknown <disabled extension, no information on IP, empty collection, empty filtered collection, IP is an exception>
     */
    public function beforeProductLoad(Varien_Event_Observer $observer) {

        if (!$this->isEnabled(Mage::app()->getStore()->getId())) {
            return;
        }

        $isHomepage = Mage::getBlockSingleton('page/html_header')->getIsHomePage();
        if ($isHomepage) {
            return;
        }

        $currentIp = Mage::helper('core/http')->getRemoteAddr(); //echo $currentIp;// this will get the visitor ip address
        $this->_getHelper()->blockBlackList($currentIp);
        $currentStore = Mage::app()->getStore()->getId(); // current store id
        //$currentIp = '58.65.183.10';//'41.99.121.142'; //'58.65.183.10'; // testing purpose
        $remoteAddr = ip2long($currentIp); // convert ip into remote address
        $infoByIp = $this->_getHelper()->getInfoByIp($currentIp); //print_r($infoByIp);exit; // get ip related info for country code and country name by remote id if exists
        if (empty($infoByIp)) {
            return;
        }
        $country = $infoByIp['cn']; // country name
        /* country based, store based collection of rules */
        $_geoipCollection = $this->_getHelper()->getRulesByCountry($country, $currentStore, false, false);
        
        if (!$_geoipCollection->count() > 0) {
            return;
        }
        
        $_filterdCollection = $this->_getHelper()->rulesByProduct($_geoipCollection, $observer->getValue(), $currentStore);
        
        //echo '<pre>';print_r($_filterdCollection->getData());exit;
        if (!count($_filterdCollection) > 0) {
            return;
        }

        foreach ($_filterdCollection as $i) {

            $ipExcepArr = explode(',', $i->getIpsException());
            /* filter an array for bad ip input */
            $ipsFilteredArr = $this->_getHelper()->validateIpFilter($ipExcepArr);
            /* if current ip is not an exception and is not blocked individually, proceed */
            if (in_array($currentIp, $ipsFilteredArr)) {
                /* first to check if rules are not available */
                return;
            }
            /* if acl being applied has rules defined, proceed */
            $this->_getHelper()->redirectOrMessage($i->getRedirectUrl(), $i->getNotes());
        }
    }

}
