<?php
/**
 * Created by PhpStorm.
 * User: December January
 * Date: 3/25/14
 * Time: 12:23 PM
 */

class VES_VendorsShipping_Model_Observer {
    
    public function ves_vendor_checkout_type_onepage_shippingmethod(Varien_Event_Observer $observer){
    	$shippingMethodObj 	= $observer->getShippingMethod();
    	$shippingMethod 	= $shippingMethodObj->getMethod();
    	if(substr($shippingMethod, 0,16) == 'vendor_multirate'){
	    	$shippingMethod = str_replace('vendor_multirate_','',$shippingMethod);
	    	$vendorId		= $observer->getVendorId();
	    	
	    	$shippingMethods 	= explode(VES_VendorsShipping_Model_Shipping::METHOD_DELEMITER, $shippingMethod);
	    	if(sizeof($shippingMethods) >= 1){
	    		foreach($shippingMethods as $method){
	    			$methodInfo = explode(VES_VendorsShipping_Model_Shipping::DELEMITER, $method);
	    			if(isset($methodInfo[1]) && $methodInfo[1] == $vendorId){
	    				$shippingMethodObj->setMethod($method);
	    			}
	    		}
	    	}
    	}
    }
}