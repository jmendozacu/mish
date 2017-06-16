<?php
/**
 * Class VES_VendorsShipping_Helper_Data
 * @author : Vnecoms
 * @date : 25/3/2014
 * @time: 01:04:00 AM
 */

class VES_VendorsShipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    public function enableFlatrateShipping(){
    	return Mage::getStoreConfig('carriers/vendor_flatrate/active');
    }

}