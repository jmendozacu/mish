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

class MageWorx_DeliveryZone_Model_Rewrite_Shipping_Rate_Result extends Mage_Shipping_Model_Rate_Result
{
    protected $_quote;
    protected $_address;
    protected $_carriers;

    /**
     * Construct object MageWorx_DeliveryZone_Model_Rewrite_Shipping_Rate_Result
     * Assing quote to object
     */
    public function __construct() {
        if(!$this->_quote) {
            if (Mage::app()->getStore()->isAdmin()) {
                $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
            } else {
                $quote = Mage::getSingleton('checkout/cart')->getQuote();           
            }
            $this->_quote = $quote;
        }
    }

    /**
     * Add a rate to the result
     * @param type $result
     * @return MageWorx_DeliveryZone_Model_Rewrite_Shipping_Rate_Result
     */
    public function append($result)
    {
        
        if(!$this->_address) {
            $this->_address = $this->getSalesAddress($this->_quote);
        }
        
        
        if ($result instanceof Mage_Shipping_Model_Rate_Result_Error) {
            $this->setError(true);
        }
        if ($result instanceof Mage_Shipping_Model_Rate_Result_Abstract) {
            $this->_rates[] = $result;
        }
        elseif ($result instanceof MageWorx_DeliveryZone_Model_Rewrite_Shipping_Rate_Result) {
            $rates = $result->getAllRates();
            foreach ($rates as $rate) {
                
                $rate = $this->validateRate($rate);
                if(!$rate->getDisable()) {
                    $this->append($rate);
                }
            }
        }
        return $this;
    }
    
    /**
     * Check right address
     * @param $quote | $order $sales
     * @return object 
     */
    public function getSalesAddress($sales) {
        $address = $sales->getShippingAddress();
        if ($address->getSubtotal()==0) {
            $address = $sales->getBillingAddress();
        }
        return $address;
    }
    
    /**
     * Get rules
     * @param object $rate
     * @return MageWorx_DeliveryZone_Model_Rates_Collection
     */
    public function getRules($rate) {
        $code = $rate->getCarrier()."_".$rate->getMethod();
        // get customerGroupId
        if (Mage::app()->getStore()->isAdmin()) {
            if (Mage::getSingleton('adminhtml/session_quote')) {
                $customerGroupId = Mage::getSingleton('adminhtml/session_quote')->getCustomer()->getGroupId();
            } else {
                $customerGroupId = 0;
            }
        } else {
            $customerGroupId = Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getCustomer()->getGroupId() : 0;            
        }
        
        $collection = Mage::getModel('deliveryzone/rates')->getCollection();
        $collection->filterByMethod($code)
                ->filterByStore(Mage::app()->getStore()->getId())
                ->filterByCustomerGroup($customerGroupId)
                ->sortList();
       // echo $collection->getSelect()->__toString();
        return $collection;
    }
    
    /**
     * Check is rate enabled in location
     * @param object $rate
     * @return boolean TRUE
     */
    private function _checkIsDisable($rate) {
        $location = Mage::helper('deliveryzone')->getLocation();
        if(!is_array($this->_carriers)) {
            $this->_carriers = array();
            $carrierCollection = Mage::getResourceModel('deliveryzone/zone_location_collection')
                    ->getCarriersByLocation($location->getCountryId());

//            echo $carrierCollection->getSelect()->__toString(); exit;
            foreach ($carrierCollection as $item) {
                foreach (explode(",",$item->getAllowedMethods()) as $_item) {
                    $this->_carriers[] = $_item;
                }
            }
            $this->_carriers = array_filter($this->_carriers); // need to clean array
        }
        if(!count($this->_carriers) && $this->_checkAssignProducts()) return true;
        $rate->setDisable(TRUE);
        if(in_array($rate->getCarrier()."_".$rate->getMethod(),$this->_carriers) && $this->_checkAssignProducts()) {
            $rate->unsDisable();    
            return true;
        }
        return;
    }
    
    /** Check if quote products can be shipped
     * 
     * @return boolean
     */
    private function _checkAssignProducts() {
        $flag = true;
        $quoteApply = array();
        $location = Mage::helper('deliveryzone')->getLocation();
        if(is_array($location->getZoneIds())) {
            $location->setZoneIds(join("-",$location->getZoneIds()));
        }
        //echo join("_",$location->getData()); exit;
        $zones = join("_",$location->getData());
        Mage::helper('deliveryzone')->registerZoneProduct();
        $productIds = Mage::registry('unavailible_shippingsuite_zone_product_'.$zones);
        $items = $this->_quote->getAllItems();
        if(!count($productIds)) {
            $productIds = array();
            foreach ($items as $item) {
                $productIds[] = $item->getProductId();
            }
            $collection = Mage::getResourceModel('deliveryzone/zone_location_collection')->loadByProductIds($productIds);
            if($collection->getSize()) {
                return false;
            } 
            return true;
        }
       // if(!$productIds) $productIds = array();
        $quoteProducts = array();
//        print_r($productIds); exit;
        foreach ($items as $item) {
            if(in_array($item->getProductId(),$productIds)) {
                $quoteApply[] = TRUE;
            }
        }
        if(count($this->_quote->getAllItems()) == count($quoteApply)){
            return TRUE;
        }
        return FALSE;
    }   

    /**
     * Validate rate
     * @param object $rate
     * @return object
     */
    public function validateRate($rate) {
        
        $this->_checkIsDisable($rate);
        $rules = $this->getRules($rate);
        $basePrice = 0;
        foreach ($rules as $rule) {
            $conditions = unserialize($rule->getConditionsSerialized());            
            if ($conditions) {
                $conditionModel = Mage::getModel($conditions['type'])->setPrefix('conditions')->loadArray($conditions);
                $result = $conditionModel->validate($this->_address);
                if($result) {
                    $action = $rule->getSimpleAction();
                    switch ($action) {
                        case "overwrite":
                            $basePrice = $rule->getShippingCost();
                            break;
                        case "surcharge":
                            $basePrice = $rate->getPrice();
                            break;
                        case "disable":
                            $rate->setDisable(TRUE);
                            return $rate;
                        case "enable":
                            return $rate;
                        case "enableandoverwrite":
                            $basePrice = $rule->getShippingCost();
                    }

                    $price = $this->_calculatePrice($rule,$basePrice);
                    $rate->setPrice($price);
                    return $rate;
                } else {
                    $action = $rule->getSimpleAction();
                    switch ($action) {
                        case "overwrite":
                        case "surcharge":
                        case "disable":
                            return $rate;
                        case "enable":
                        case "enableandoverwrite":
                            $rate->setDisable(TRUE);
                            return $rate;
                    }
                }
            }
        }
        return $rate; 
    }
    
    /**
     * Calculate rate price
     * @param object $rule
     * @param float $basePrice
     * @return float
     */
    private function _calculatePrice($rule,$basePrice) {
        $address    = $this->_address;
        $quote      = $this->_quote;
        $productQty = $quote->getItemsCount();
        $itemQty    = $quote->getItemsQty();
        $subtotal   = $address->getSubtotalInclTax()?$address->getSubtotalInclTax():$address->getSubtotal();
        $weight     = $address->getWeight()?$address->getWeight():1;
        $price      = $basePrice;
        
        $price += $rule->getSurchargeFixed();
        $price += $rule->getSurchargePercent()/100*$basePrice;
        $price += $rule->getFixedPerProduct()*$productQty;
        $price += $rule->getPercentPerProduct()/100*$basePrice*$productQty;
        $price += $rule->getPercentPerItem()/100*$basePrice*$itemQty;
        $price += $rule->getFixedPerItem()*$itemQty;
        $price += $rule->getPercentPerOrder()/100*$subtotal;
        $price += $rule->getFixedPerWheight()*$weight;
        return $price;
    }
}
