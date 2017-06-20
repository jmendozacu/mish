<?php
class Mish_Shipit_Block_Checkout_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping {
    protected $_vendor_rates;
    
    
    public function getRatesByVendor(){
        if (empty($this->_vendor_rates)) {
            $groups = array();
            $rateCollection = $this->getAddress()->getShippingRatesCollection();
            foreach($rateCollection as $rate){
                if($rate->isDeleted()) continue;
                if($rate->getCarrier() == 'vendor_multirate') continue;

                $tmp = explode(VES_VendorsShipping_Model_Shipping::DELEMITER, $rate->getCode());
                if(sizeof($tmp) != 2) continue;
                $vendorId = $tmp[1];
                $vendor = Mage::getModel('vendors/vendor')->load($vendorId);
                if(!$vendor->getId()) continue;
                if(!isset($groups[$vendorId])) $groups[$vendorId] = array();
        
                $groups[$vendorId]['title'] = $vendor->getTitle();
                if(!isset($groups[$vendorId]['rates'])) $groups[$vendorId]['rates'] = array();
                $groups[$vendorId]['rates'][] = $rate;
            }
            ksort($groups);
            $this->_vendor_rates = $groups;
        }
        return $this->_vendor_rates;
    }
    function getSelectedVendorShipping(){
    	$selectedMethod = str_replace("vendor_multirate_", '', $this->getAddressShippingMethod());
    	$selectedMethods = explode(VES_VendorsShipping_Model_Shipping::METHOD_DELEMITER, $selectedMethod);
    	return $selectedMethods;
    }
}