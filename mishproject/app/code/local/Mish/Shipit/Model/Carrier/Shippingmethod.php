
<?php
// app/code/local/Envato/Customshippingmethod/Model
class Mish_Shipit_Model_Carrier_Shippingmethod
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'shipit';
 
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    $result = Mage::getModel('shipping/rate_result');
    $result->append($this->_getDefaultRate());

     if($request->getAllItems()) {
      foreach($request->getAllItems() as $item) {
        if($item->getParentItem()) continue;
         $product = Mage::getModel('catalog/product')->load($item->getProductId());
         $vendorId = $product->getVendorId();
         /*echo "<pre>++";
         print_r($product->getData());
         echo "</pre>";*/

      }
    }

    return $result;
  }
    



 
  public function getAllowedMethods()
  {

    
    return array(
      'shipit' => $this->getConfigData('name'),
    );
  }
 
  protected function _getDefaultRate($vendorId)
  {
    $rate = Mage::getModel('shipping/rate_result_method');

     $rate->setVendorId($vendorId);
             
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($this->getConfigData('title'));
    $rate->setMethod($this->_code);
    $rate->setMethodTitle($this->getConfigData('name'));
    $estimatedprice  = $this->getPriceDetails();

    $rate->setPrice($estimatedprice);
    $rate->setCost(0);
    
    return $rate;
  }
   

 protected function getPriceDetails()
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


            $shipitmain = Mage::getModel('shipit/shipitmaintable')->getCollection()
          ->addFieldToFilter('postcode', array('eq' => $postcod));
                  foreach ($shipitmain as $estimates) {
                    $tableWeight = $estimates['shipit_package_weight'];
                    $compareWeight = explode('-',$tableWeight);

                    $weightPosZero =  $compareWeight[0];
                    $weightPosOne  = $compareWeight[1];

                    if(($weight > $weightPosZero) && ($weight <= $weightPosOne)){
                         $calculatedPrice[] = ($estimates['shipit_package_price'] *  $quantity);
                        
                    }
                    
                }

               }  
               $estimatedprice = array_sum($calculatedPrice);
               return $estimatedprice ;     
   } 

 public function getShippingDeatils()
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

          $shipitmain = Mage::getModel('shipit/shipitmaintable')->getCollection()
          ->addFieldToFilter('postcode', array('eq' => $postcode));
          
           
                  foreach ($shipitmain as $estimate) {
                    $tableWeight = $estimate['shipit_package_weight'];
                    $compareWeight = explode('-',$tableWeight);

                    $weightPosZero =  $compareWeight[0];
                    $weightPosOne  = $compareWeight[1];

                    if(($weight > $weightPosZero) && ($weight <= $weightPosOne)){
                    $calculatedPrice = ($estimate['shipit_package_price'] *  $quantity);
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
