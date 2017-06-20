<?php
/**
 * Created by PhpStorm.
 * User: December January
 * Date: 3/25/14
 * Time: 12:23 PM
 */

class VES_VendorsShipping_Model_Shipping extends Mage_Shipping_Model_Shipping{
    /*Characters between method and vendor_id*/
    const DELEMITER = '||';
    
    /*Characters between methods*/
    const METHOD_DELEMITER = '|_|';
    
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request){
        parent::collectRates($request);
        
        if(Mage::helper('vendors')->getMode() == VES_Vendors_Model_Vendor::MODE_ADVANCED){
            return $this;            
        }
        
        $shippingRates = $this->getResult()->getAllRates();
        $newVendorRates = array();
        foreach ($this->gtoupShippingRatesByVendor($shippingRates) as $vendorId=>$rates) {
            if(!sizeof($newVendorRates)){
                foreach($rates as $rate){
                    $newVendorRates[$rate->getCarrier().'_'.$rate->getMethod()] = $rate->getPrice();
                }
            }else{
                $tmpRates = array();
                foreach($rates as $rate){
                    foreach($newVendorRates as $cod=>$shippingPrice){
                        $tmpRates[$cod.self::METHOD_DELEMITER.$rate->getCarrier().'_'.$rate->getMethod()] = $shippingPrice+$rate->getPrice();
                    }
                }
                $newVendorRates = $tmpRates;
            }
        }
        foreach($newVendorRates as $code=>$shippingPrice){
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier('vendor_multirate');
            $method->setCarrierTitle(Mage::helper('vendorsshipping')->__('Multiple Rate'));
             
            $method->setMethod($code);
            $method->setMethodTitle(Mage::helper('vendorsshipping')->__('Shipping'));
             
            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
            $this->getResult()->append($method);
        }
        
        return $this;
    }
    
    /**
     * Group shipping rates by each vendor.
     * @param unknown $shippingRates
     */
    public function gtoupShippingRatesByVendor($shippingRates){
        $rates = array();
        foreach($shippingRates as $rate){
            if(!$rate->getVendorId()) continue;
            if(!isset($rates[$rate->getVendorId()])){
                $rates[$rate->getVendorId()] = array();
            }
            $rates[$rate->getVendorId()][] = $rate;
        }
        ksort($rates);
        return $rates;
    }
}