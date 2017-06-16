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
 * Delivery Zone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_DeliveryZone_Block_Product_View_Shippinglist extends Mage_Core_Block_Template
{
    public $zones;
    public function getRestrictedList() {
        $list = array();
        $countries = array();
        $zones = Mage::helper('deliveryzone')->getLocation()->getZoneIds();
        $this->zones = $zones;
        $product_id = Mage::registry('current_product')->getId();
        $collection = Mage::getResourceModel('deliveryzone/zone_location_collection')->loadByProductId($product_id);
        $collection->getSelect()->group("country_id");
        $collectionCountries = Mage::getModel('directory/country')->getCollection(); 
        foreach ($collectionCountries as $country) {
            $countries[$country->getId()] = $country->getName();
        }
          if($collection->getSize()>0) {
            foreach ($collection as $item) {
                $list[] = $countries[$item->getCountryId()];
            }
            
        } else {
            $collection = Mage::getResourceModel('deliveryzone/zone_location_collection')->loadByProductId($product_id);
            if($collection->getSize()) {
                foreach ($collection as $item) {
                    unset($countries[$item->getCountryId()]);
                }
                $list = $countries;
            } else {
                $list[] = $this->__('Worldwide');
            }
        }
      
      
        return $list;
    }
    
    public function productNoticeUnavailible() {
        
        $zones = Mage::helper('deliveryzone')->registerZoneProduct(Mage::registry('current_product')->getId());
//        echo "<pre>"; var_dump(Mage::registry('unavailible_shippingsuite_zone_product_'.$zones)); exit;
        if(is_array(Mage::registry('unavailible_shippingsuite_zone_product_'.$zones)) && 
           !in_array(Mage::registry('current_product')->getId(),Mage::registry('unavailible_shippingsuite_zone_product_'.$zones))) {
            Mage::register('shippingsuite_checkout_disable', true, true);
            return Mage::helper('core')->__('*Please note that this product cannot be shipped to your country');
        }
    }
}