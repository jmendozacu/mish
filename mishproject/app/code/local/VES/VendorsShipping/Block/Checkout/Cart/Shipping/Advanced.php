<?php
class VES_VendorsShipping_Block_Checkout_Cart_Shipping_Advanced extends VES_VendorsCheckout_Block_Cart_Shipping {
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
    
    public function getEstimateRates(){
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
}