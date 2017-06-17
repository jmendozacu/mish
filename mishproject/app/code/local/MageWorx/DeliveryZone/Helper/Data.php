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

class MageWorx_DeliveryZone_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_location = null;

    /**
     * Set current location
     * @param array $data
     */
    public function setLocation(array $data)
    {
        $data = array_intersect_key($data, array('country_id' => '', 'region_id' => '', 'region' => ''));
        $cookie = Mage::getSingleton('core/cookie');
        $cookie->set('shipping_region', rawurlencode(base64_encode(serialize($data))), true, '/', null, null, true);
        if($quoteId = Mage::getSingleton('checkout/session')->getQuoteId()) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            
            $shipping = $quote->getShippingAddress();
            $shipping->setCountryId(isset($data['country_id'])?$data['country_id']:'');
            $shipping->setRegionId(isset($data['region_id'])?$data['region_id']:'');
            $shipping->setRegion(isset($data['region'])?$data['region']:'');
            $quote->save();
        }
        $this->_location = new Varien_Object();
        $this->_location->setData($data);
        $this->_setLocationDetails();
    }

    /**
     * Get location
     * @return object
     */
    public function getLocation()
    {
        $data = array();
        if (!$this->_location instanceof Varien_Object){
            if(Mage::getStoreConfigFlag('mageworx_deliveryzone/deliveryzone/autodetection')) {
                $currentLocation = Mage::getModel('mwgeoip/geoip')->getCurrentLocation();
                $data['country_id'] = $currentLocation->getCode();
                $data['region_id']  = Mage::getModel('directory/region')->loadByName($currentLocation->getRegion(),$currentLocation->getCode())->getRegionId();
                $data['region']  = $currentLocation->getRegion();
            }
            $dataString = $this->_getRequest()->getCookie('shipping_region', null);
            if ($dataString){
                $data = unserialize(base64_decode(rawurldecode($dataString)));
            }
            if(Mage::getSingleton('checkout/session')->getQuoteId()) {
                $shipping = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
                $data['country_id'] = $shipping->getCountryId() ? $shipping->getCountryId() : $data['country_id'];
                if(!isset($data['region_id'])) { 
                    $data['region_id'] = "";
                }
                $data['region_id']  = $shipping->hasRegionId() ? $shipping->getRegionId() : $data['region_id'];
                $data['region']  = $shipping->getRegion() ? $shipping->getRegion() : $data['region'];
            }
            
            $this->_location = new Varien_Object();
            $this->_location->setData($data);
            $this->_setLocationDetails();
        //     echo "<pre>"; print_r($this->_location); echo "</pre>";
        }
       
        return $this->_location;
    }

    /**
     * Add location details and zone ids
     * Add text in object
     */
    private function _setLocationDetails()
    {
        if ($countryId = $this->_location->getCountryId()){
            $country = Mage::getModel('directory/country')->load($countryId);
            $this->_location->setCountryName($country->getName());
        }
        if ($regionId = $this->_location->getRegionId()){
            $region = Mage::getModel('directory/region')->load($regionId);
            $this->_location->setRegionName($region->getName());
        }
        elseif ($region = $this->_location->getRegion()){
            $this->_location->setRegionName($region);
            $this->_location->setRegionId('');
        }
        // add zones id
        $zoneIds = Mage::getModel('deliveryzone/zone')->loadZoneByLocation($this->_location->getCountryId(),$this->_location->getRegionId());
        $this->_location->setZoneIds($zoneIds);
    }

    /**
     * Check location
     * @param array $data
     * @return boolean
     */
    public function isLocationEqual(array $data)
    {
        $location = new Varien_Object();
        $location->setData($data);
        $zoneLocation = $this->getLocation();
        if ($zoneLocation->getCountryId() == $location->getCountryId() &&
        	$zoneLocation->getRegionId() == $location->getRegionId()) {
            return true;
        }
        return false;
    }
    
    /**
     * Register products in current zones
     */
    public function registerZoneProduct($productId = false) {
        $location = Mage::helper('deliveryzone')->getLocation();
        if(is_array($location->getZoneIds())) {
            $location->setZoneIds(join("-",$location->getZoneIds()));
        }
        //echo join("_",$location->getData()); exit;
        $zones = join("_",$location->getData());
    
        if(!Mage::registry('unavailible_shippingsuite_zone_product_'.$zones)) {
           $zone = Mage::getModel('deliveryzone/zone');
           $productIds = $zone->getProductIdsByLocation($location->getZoneIds());
            if(count($productIds)) {
                Mage::register('unavailible_shippingsuite_zone_product_'.$zones, $productIds,true);
            } else {
                if(!Mage::registry('current_product') && !$productId) {
                   return Mage::register('unavailible_shippingsuite_zone_product_'.$zones, array(),true);
                } 
                if(Mage::registry('current_product')) {
                    $_productId = Mage::registry('current_product')->getId();
                } {
                    $_productId = $productId;
                }
                $zoned = $zone->checkProductOnAvailible($_productId);
                if($zoned) {
                    Mage::unregister('unavailible_shippingsuite_zone_product_'.$zones);
                    Mage::register('unavailible_shippingsuite_zone_product_'.$zones, array(),true);
                } else {
                    Mage::unregister('unavailible_shippingsuite_zone_product_'.$zones);
                    Mage::register('unavailible_shippingsuite_zone_product_'.$zones, false,true);
                }
            }
        }
        return $zones;
    }
}