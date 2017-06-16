<?php
/**
 * Class VES_VendorsFlatrate_Helper_Data
 * @author : Vnecoms
 * @date : 25/3/2014
 * @time: 01:04:00 AM
 */

class VES_VendorsFlatrate_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    public function enableFlatrateShipping(){
    	return Mage::getStoreConfig('carriers/vendor_flatrate/active');
    }
    
    
    
    public function getFlatRateEnabled($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/flatrate/enabled", $vendor_id);
    }

    public function getFreeShippingEnabled($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/freeshipping/enabled", $vendor_id);
    }

    public function getFlatRateTitle($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/flatrate/title", $vendor_id);
    }

    public function getFreeShippingTitle($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/freeshipping/title", $vendor_id);
    }

    public function getFlatRateName($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/flatrate/name", $vendor_id);
    }

    public function getFreeShippingName($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/freeshipping/name", $vendor_id);
    }

    public function getFlatRateType($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/flatrate/type", $vendor_id);
    }

    public function getFlatRatePrice($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/flatrate/price", $vendor_id);
    }

    public function getFreeShippingSubTotal($vendor_id) {
        return Mage::helper("vendorsconfig")->getVendorConfig("shipping/freeshipping/free_shipping_subtotal", $vendor_id);
    }

}