<?php
class VES_VendorsShipping_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available {
    protected $_vendor_rates;
    /**
     * Sort rates recursive callback
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortRates($a, $b)
    {
        if ((int)$a[0]->carrier_sort_order < (int)$b[0]->carrier_sort_order) {
            return -1;
        } elseif ((int)$a[0]->carrier_sort_order > (int)$b[0]->carrier_sort_order) {
            return 1;
        } else {
            return 0;
        }
    }
    public function prepareTemplate(){
        if(Mage::helper('vendors')->getMode() == VES_Vendors_Model_Vendor::MODE_ADVANCED){
            $this->setTemplate('checkout/onepage/shipping_method/available.phtml');
            return;
        }
    }

    public function getShippingRates(){
        $this->getAddress()->collectShippingRates()->save();
        if (empty($this->_rates)) {
            $rates = array();
            foreach ($this->getAddress()->getShippingRatesCollection() as $rate) {
                if (!$rate->isDeleted() && $rate->getCarrierInstance()) {
                    if(strpos($rate->getMethod(), VES_VendorsShipping_Model_Shipping::DELEMITER) === false) continue;
    
                    if (!isset($rates[$rate->getCarrier()])) {
                        $rates[$rate->getCarrier()] = array();
                    }
    
                    $rates[$rate->getCarrier()][] = $rate;
                    $rates[$rate->getCarrier()][0]->carrier_sort_order = $rate->getCarrierInstance()->getSortOrder();
                }
            }
            uasort($rates, array($this, '_sortRates'));
    
            $this->_rates = $rates;
        }
        return $this->_rates;
    }
    
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