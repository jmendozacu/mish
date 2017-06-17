<?php
// app/code/local/Envato/Customshippingmethod/Model
class Mish_Blueexpress_Model_Carrier_Shippingblueexpress
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'blueexpress';
 
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    $result = Mage::getModel('shipping/rate_result');
    $result->append($this->_getDefaultRate());

    return $result;
  }
 
  public function getAllowedMethods()
  {
    return array(
      'blueexpress' => $this->getConfigData('name'),
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
    $estimatedprice  = $this->getBluexPriceDetails();

    $rate->setPrice($estimatedprice);
    $rate->setCost(0);
    
    return $rate;
    
  }

   protected function getBluexPriceDetails()
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


           $bluexmain = Mage::getModel('blueexpress/blueexpressmaintable')->getCollection()
          ->addFieldToFilter('postcode', array('eq' => $postcod));
                  foreach ($bluexmain as $estimates) {
                    $tableWeight = $estimates['bluex_package_weight'];
                    $compareWeight = explode('-',$tableWeight);

                    $weightPosZero =  $compareWeight[0];
                    $weightPosOne  = $compareWeight[1];

                    if(($weight > $weightPosZero) && ($weight <= $weightPosOne)){
                         $calculatedPrice[] = ($estimates['bluex_package_price'] *  $quantity);
                        
                    }
                    
                }

               }  
               $estimatedprice = array_sum($calculatedPrice);
               return $estimatedprice ;   
   } 

     public function getShippingDetails()
   {
          $quotes = Mage::getSingleton('checkout/session')->getQuote();
          $billingAddres = $quotes->getBillingAddress();

          $quote = Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection();
        
            foreach($quote as $item) {

            $quantity = $item->getQty();
            $price = $item->getPrice();
          $weight = $item->getWeight();
            $name = $item->getName();
           

          $postcode = $billingAddres->getPostcode();

          $bluexmain = Mage::getModel('blueexpress/blueexpressmaintable')->getCollection()
          ->addFieldToFilter('postcode', array('eq' => $postcode));
          
           
                  foreach ($bluexmain as $estimate) {
                    $tableWeight = $estimate['bluex_package_weight'];
                    $compareWeight = explode('-',$tableWeight);

                    $weightPosZero =  $compareWeight[0];
                    $weightPosOne  = $compareWeight[1];

                    if(($weight > $weightPosZero) && ($weight <= $weightPosOne)){
                    $calculatedPrice = ($estimate['bluex_package_price'] *  $quantity);
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