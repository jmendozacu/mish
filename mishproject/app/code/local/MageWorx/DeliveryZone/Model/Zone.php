<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_DeliveryZone_Model_Zone extends Mage_Core_Model_Abstract
{
    const ZONE_TYPE_CATEGORY = 1; # depricated by Shipping suite v 1.0.0
    const ZONE_TYPE_SHIPPINGMETHOD = 2; # depricated by Shipping suite v 1.0.0
    private $_zoneIds = array();

    public function _construct()
    {
        $this->_init('deliveryzone/zone');
    }
    
    /**
     * Save parent items for zone (countries w/regions, shipping methods, categories and products).
     */
    public function _afterSave() {
        parent::_afterSave();
        if(Mage::registry('deliveryzone_massaction_status')) return;
        $itemCountries          = $this->getCountries();
        $itemRegions            = $this->getRegions();
        // save zone countries
        $countryModel = Mage::getModel('deliveryzone/zone_location');
            // remove all items by zone id
            foreach ($countryModel->getCollection()->loadByZoneId($this->getId()) as $item) {
                $item->delete();
            }
        
        foreach ($itemCountries as $country) {
            $regionIds = '';
            if(isset($itemRegions[$country])) {
                $regionIds = join(',',$itemRegions[$country]);
            }
            $countryModel->setData(array('zone_id'=>$this->getId(),'country_id'=>$country,'region_ids'=>$regionIds))->save();
        }
        
        $itemProducts           = explode(',', $this->getProductIds());
        $itemProducts           = array_filter($itemProducts);
        $itemProducts           = array_unique($itemProducts);
        // save zone products
        $productModel = Mage::getModel('deliveryzone/zone_product');
        // remove all items by zone id
        foreach ($productModel->getCollection()->loadByZoneId($this->getId()) as $item) {
            $item->delete();
        }
        
        foreach ($itemProducts as $product) {
            $productModel->setData(array('zone_id'=>$this->getId(),'product_id'=>$product))->save();
        }
        
        if($this->hasCategoryIds()) {
            $itemCategories         = explode(",",$this->getCategoryIds());
            $itemCategories         = array_filter($itemCategories);

            //save zone categories
            $categoryModel = Mage::getModel('deliveryzone/zone_category');
            // remove all items by zone id
            foreach ($categoryModel->getCollection()->loadByZoneId($this->getId()) as $item) {
                $item->delete();
            }

            foreach ($itemCategories as $category) {
                $categoryModel->setData(array('zone_id'=>$this->getId(),'category_id'=>$category))->save();
            }
        }
        
        $itemShippingMethods    = $this->getActive();
        if($itemShippingMethods) {
            $itemShippingSubMethods = $this->getMethods();
            // save zone shipping methods
            $sMethodModel = Mage::getModel('deliveryzone/zone_shippingmethod');
                // remove all items by zone id
                foreach ($sMethodModel->getCollection()->loadByZoneId($this->getId()) as $item) {
                    $item->delete();
                }

            foreach ($itemShippingMethods as $method=>$isActive) {
                if(!$isActive) continue;

                $subMethodIds = '';
                if(isset($itemShippingSubMethods[$method])) {
                    $subMethodIds = join(',',$itemShippingSubMethods[$method]);
                }
                $sMethodModel->setData(array('zone_id'=>$this->getId(),'carrier_id'=>$method,'allowed_methods'=>$subMethodIds))->save();
            }
        }
    }
    
    /**
     * Get zone ids by country and region ids
     * @param string $countryId
     * @param string|bool $regionId
     * @return array
     */
    public function loadZoneByLocation($countryId,$regionId=false) {
        $zoneIds = array();
        $collection = Mage::getResourceModel('deliveryzone/zone_location_collection')->loadZoneByLocation($countryId);
        foreach ($collection as $item) {
            if($regionId && !in_array($regionId, explode(',', $item->getRegionIds()))) {
                continue;
            }
            $zoneIds[] = $item->getZoneId();
        }
        //echo "<pre>"; print_r($zoneIds); exit;
        return $zoneIds;
    }
    
    /**
     * Get category ids by zone ids
     * @param array $zoneIds
     * @return array
     */
    public function getCategoryIdsByLocation($zoneIds=array()) {
        if(count($zoneIds)<1) return array();
        
        $categoryIds = array();
        $collection = Mage::getResourceModel('deliveryzone/zone_category_collection')->loadByZoneIds($zoneIds);
        foreach ($collection as $item) {
            $categoryIds[] = $item->getCategoryId();
        }
        return $categoryIds;
    }
    
    /**
     * Get product ids by zone ids
     * @param array $zoneIds
     * @return array
     */
    public function getProductIdsByLocation($zoneIds=array()) {
        if(count($zoneIds)<1) return array();
        
        $productIds = array();
        $collection = Mage::getResourceModel('deliveryzone/zone_product_collection')->loadByZoneIds($zoneIds);
        foreach ($collection as $item) {
            $productIds[] = $item->getProductId();
        }
        return $productIds;
    }
    
    /** 
     * Check product in all zones
     * @param int $productId
     * @return boolean
     */
    public function checkProductOnAvailible($productId) {
        $collection = Mage::getResourceModel('deliveryzone/zone_product_collection')->loadByProductId($productId);
        if($collection->getSize()) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Get shipping method collection
     * @param array $zoneIds
     * @return object
     */
    private function _getCarrierByLocation($zoneIds=array()) {
        if(count($zoneIds)<1) return array();
        $carrierIds = array();
        $collection = Mage::getResourceModel('deliveryzone/zone_shippingmethod_collection')->loadByZoneIds($zoneIds);
        
        foreach ($collection as $item) {
            $carrierIds[$item->getCarrierId()] = explode(',',$item->getAllowedMethods());
        }
        
        return $carrierIds;
    }
    
    /**
     * Get carrier by category and products
     * @return object
     */
    public function getCarrierByCategoryAndProducts() {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if(Mage::app()->getStore()->isAdmin()) {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }
        $shippingItems = $quote->getAllVisibleItems();
        foreach ($shippingItems as $item) {
            $item = Mage::getModel('catalog/product')->load($item->getProductId());
            $categoryIds = $item->getCategoryIds();
            $collection = Mage::getResourceModel('deliveryzone/zone_category_collection')->loadByCategoryIds($categoryIds);
          //  echo $collection->getSelect()->__toString(); exit;
            if($collection->getSize()) {
                foreach($collection as $_item) {
                    $this->_zoneIds[$_item->getZoneId()] = $_item->getZoneId();
                }
                continue;
            } else {
                return array();
            }
            $collection = Mage::getResourceModel('deliveryzone/zone_product_collection')->loadByProductId($item->getId());
          //  echo $collection->getSelect()->__toString(); exit;
            if($collection->getSize()) {
                foreach($collection as $_item) {
                    $this->_zoneIds[$_item->getZoneId()] = $_item->getZoneId();
                }
            } else {
                return array();
            }
        }
       // echo 42234; exit;
        $carriers = $this->_getCarrierByLocation($this->_zoneIds);
        return $carriers;
    }
    
    /**
     * Get shipping method ids by zone ids
     * @param array $zoneIds
     * @return array
     */
    public function getCarrierByLocation($zoneIds=array()) 
    {
        $zoneCarriers = array();
        $carrierIds = array();
        $zoneCarriers = $this->_getCarrierByLocation($zoneIds);
        if(Mage::getStoreConfigFlag('mageworx_deliveryzone/deliveryzone/customergroup_carrier'))
        {
            $customerGroupCarriers = array();
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            $collection = Mage::getResourceModel('deliveryzone/customer_group_collection')->loadByGroupId($customerGroupId);
            if($collection->getSize()<1) {
                return $carrierIds = $zoneCarriers;
            }
            
            foreach ($collection as $item) {
                $customerGroupCarriers[$item->getCarrierId()] = explode(',',$item->getAllowedMethods());
            }
            if(count($zoneCarriers)<1)
            {
                $carrierIds = $customerGroupCarriers;
            }
            else {
                foreach ($customerGroupCarriers as $code=>$item) {
                    if(isset($zoneCarriers[$code]))
                    {
                        $carrierIds[$code] = array_intersect($zoneCarriers[$code], $item);
                    }
                }
            }
        } else {
            $carrierIds = $zoneCarriers;
        }
        return $carrierIds;
    }
}