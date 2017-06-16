<?php

class Mish_Correoschile_Model_Carrier_Shippingcorreoschile
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'correoschile';
 
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    $result = Mage::getModel('shipping/rate_result');
    $result->append($this->_getDefaultRate());

    return $result;
  }
 
  public function getAllowedMethods()
  {
    return array(
      'correoschile' => $this->getConfigData('name'),
    );
  }
   protected function _getDefaultRate()
  {

     $rate = Mage::getModel('shipping/rate_result_method');

     $rate->setVendorId($vendorId);
             
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($this->getConfigData('title'));
    $rate->setMethod($this->_code);
    $rate->setMethodTitle($this->getConfigData('name'));
    $estimatedprice  = $this->getCorreosPriceDetails();

    $rate->setPrice($estimatedprice);
    $rate->setCost(0);
    
    return $rate;
    
  }

   protected function getCorreosPriceDetails()
   {
$quotess = Mage::getSingleton('checkout/session')->getQuote();
            $billingAddress = $quotess->getBillingAddress();
          /*  $country = $billingAddress->getCountryId();
              $city = $billingAddress->getCity();*/
            $postcod = $billingAddress->getPostcode();
            
            $quote = Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection();
               foreach($quote as $item) {
                  $quantity = $item->getQty();
                  $price = $item->getPrice();
                  $weight = $item->getWeight();

 $correosmain = Mage::getModel('correoschile/correosmaintable')->getCollection()
          ->addFieldToFilter('postcode', array('eq' => $postcod));
                  foreach ($correosmain as $estimates) {
                    $tableWeight = $estimates['correos_package_weight'];
                    $compareWeight = explode('-',$tableWeight);

                    $weightPosZero =  $compareWeight[0];
                    $weightPosOne  = $compareWeight[1];

                    if(($weight > $weightPosZero) && ($weight <= $weightPosOne)){
                         $calculatedPrice[] = ($estimates['correos_package_price'] *  $quantity);
                        
                    }
                    
                }

               }  
               $estimatedprice = array_sum($calculatedPrice);
               return $estimatedprice ;    
   } 

     public function getShippingDetails()
   {
          $quote = Mage::getSingleton('checkout/session')->getQuote();
          $billingAddre = $quote->getBillingAddress();

          $quote = Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection();
        
            foreach($quote as $item) {

            $quantity = $item->getQty();
            $price = $item->getPrice();
           $weight = $item->getWeight();
             $name = $item->getName();

          $postcode = $billingAddre->getPostcode();

          $correosmain = Mage::getModel('correoschile/correosmaintable')->getCollection()
          ->addFieldToFilter('postcode', array('eq' => $postcode));
          
           
                  foreach ($correosmain as $estimate) {
                    $tableWeight = $estimate['correos_package_weight'];
                    $compareWeight = explode('-',$tableWeight);

                    $weightPosZero =  $compareWeight[0];
                    $weightPosOne  = $compareWeight[1];

                    if(($weight > $weightPosZero) && ($weight <= $weightPosOne)){

                      $calculatedPrice = ($estimate['correos_package_price'] *  $quantity);
                        echo "Product name- ". $name."<br>";
                      echo __("Estimate cost is ".$calculatedPrice." ".Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol()." for number of quantity ".$quantity.".")."<br>";
                     
                      echo __("Estimate time ".$estimate['days_to_delivery']." day(s)");
                       echo "<br>";
                        echo "<br>";

                    }
                    
                }

            } 
        
  }
}