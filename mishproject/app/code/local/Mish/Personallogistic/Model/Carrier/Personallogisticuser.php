<?php
// app/code/local/Envato/Customshippingmethod/Model
class Mish_Personallogistic_Model_Carrier_Personallogisticuser
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'personallogistic';
 
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
      'personallogistic' => $this->getConfigData('name'),
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
    $rate->setMethod($this->_code);
   // $estimatedprice  = $this->getPriceDetails();
   $sessionusercost = Mage::getSingleton('core/session')->getPluserCost();
    $rate->setPrice($sessionusercost);
    $rate->setCost(0);
      //Mage::getSingleton('core/session')->unsPluserCost();
    return $rate;
    //  Mage::getSingleton('core/session')->unsPluserCost();

    //Mage::getSingleton('core/session')->unsPluserCost();
  }
   

 protected function getPriceDetails()
   {

               
          
   } 

 public function getShippingDetails()
   {
   
   $quote = Mage::getSingleton('checkout/session')->getQuote();
     $_weight = $quote->getShippingAddress()->getWeight();
           $shippingcity = $quote->getShippingAddress()->getCity();
         $shippingstreet = $quote->getShippingAddress()->getStreetFull();
         $shippingregion = $quote->getShippingAddress()->getRegion();
         $shippingcountry = $quote->getShippingAddress()->getCountry();
          $shippingpostcode=$quote->getShippingAddress()->getPostcode();

        $ShippingAddress=($shippingstreet.",".$shippingcity.",".$shippingregion.",".$shippingcountry);

                            //customer  longitudea nd latitude
                             $address123 = str_replace(" ", "+",$ShippingAddress);
                            
                           $url = "http://maps.google.com/maps/api/geocode/json?address=$address123";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);
  curl_close($ch);
  $response_a = json_decode($response);
   $latitude3 = $response_a->results[0]->geometry->location->lat;
  
  $longitude3 = $response_a->results[0]->geometry->location->lng;

  


                  $cartItems = $quote->getAllVisibleItems();
                  

                  foreach ($cartItems as $item) {

       $productId = $item->getProductId();
                      $product = Mage::getModel('catalog/product')->load($productId);
                    
                        $vendorid=$product['vendor_id'];
                       $vendorcollection=Mage::getModel('inventoryplus/warehouse')->load($vendorid,'vendor_id');
                       
                       $countryid=$vendorcollection['country_id'];
                      $stateid=$vendorcollection['state_id'];

                       $region1 = Mage::getModel('directory/region')->load($stateid);
                      $state_name = $region1->getName();
                     
                        $country = Mage::getModel('directory/country')->loadByCode($countryid);
                         $countryname= $country->getName();

                     

                   $vendorwarehouseaddress=($vendorcollection['street'].",".$vendorcollection['city'].",".$state_name.",".$countryname.",".$vendorcollection['postcode']);

                        //warehouse longitudea nd latitude
                        $vendorwarehouseaddress123 = str_replace(" ", "+",$vendorwarehouseaddress);

                           $url = "http://maps.google.com/maps/api/geocode/json?address=$vendorwarehouseaddress123";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);
  curl_close($ch);
  $response_a = json_decode($response);
   $latitude2 = $response_a->results[0]->geometry->location->lat;
 
   $longitude2 = $response_a->results[0]->geometry->location->lng;
   
}

    $pluserModel = Mage::getModel('personallogistic/personallogistic')->getCollection()
    ->addFieldToFilter('status',array('eq'=>1));

    
    $sesPLuserId = Mage::getSingleton('core/session')->getPluserRadioID();

    $i=0;
    foreach ($pluserModel as $data)
    {
      

       $userid =  $data['personallogistic_id'];
      
       $personallogisticaddress= $data->getRegion();
        $personallogisticprice= $data->getPrice();
       $transportweight=$data->getTransportweight();
        $personallogistictransport= strtolower($data->getTransport());
          $personallogistictransport1 = str_replace(" ", "+", $personallogistictransport);

           //personallogistic longitudea nd latitude
        $personallogisticaddress1 = str_replace(" ", "+",$personallogisticaddress);
                         $url = "http://maps.google.com/maps/api/geocode/json?address=$personallogisticaddress1";


  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);
  curl_close($ch);
  $response_a = json_decode($response);
   $latitude1 = $response_a->results[0]->geometry->location->lat;

   $longitude1 = $response_a->results[0]->geometry->location->lng;



                         $earthRadius = 6371 ;
                $latFrom = deg2rad($latitude3);
              $lonFrom = deg2rad($longitude3);
              $latTo = deg2rad($latitude1);
              $lonTo = deg2rad($longitude1);

              $latDelta = $latTo - $latFrom;
              $lonDelta = $lonTo - $lonFrom;

              $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
               $shippingtologistic= $angle * $earthRadius."<br>";
              

           // count total distance between two logitude               

                $earthRadius = 6371 ;
                $latFrom = deg2rad($latitude1);
              $lonFrom = deg2rad($longitude1);
              $latTo = deg2rad($latitude2);
              $lonTo = deg2rad($longitude2);

              $latDelta = $latTo - $latFrom;
              $lonDelta = $lonTo - $lonFrom;

              $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
               $logistictowarehouse= $angle * $earthRadius."<br>";


              $latFrom1 = deg2rad($latitude2);
              $lonFrom1 = deg2rad($longitude2);
              $latTo1 = deg2rad($latitude3);
              $lonTo1 = deg2rad($longitude3);

              $latDelta1 = $latTo1 - $latFrom1;
              $lonDelta1 = $lonTo1 - $lonFrom1;

              $angle1 = 2 * asin(sqrt(pow(sin($latDelta1 / 2), 2) +
                cos($latFrom1) * cos($latTo1) * pow(sin($lonDelta1 / 2), 2)));
              $warehousetoshipping= $angle1 * $earthRadius;

               $totaldistance=$logistictowarehouse+$warehousetoshipping;

               $distancepricetotal=$totaldistance*$personallogisticprice;

               
                // end here
             
            //estimate time calculation start here
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$personallogisticaddress1."&destinations=".$vendorwarehouseaddress123."&mode=".$personallogistictransport1."&language=us-EN",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "GET",
                      CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "postman-token: aa56e92b-0927-1eae-bfba-614ca1cb50c7"
                      ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                      echo "cURL Error #:" . $err;
                    } else {
                      $distanceres= json_decode($response);
                      
                      $dayhrs=explode(' ', $distanceres->rows[0]->elements['0']->duration->text);
                      // echo "<pre>+++";
                      // print_r($dayhrs);
                      // echo "<br>";
                      
                     
                      if ($dayhrs['1']=='day' || $dayhrs['1']=='days') {
                      $logistictowarehousemin=($dayhrs['0']*24*60)+($dayhrs['2']*60)+($dayhrs['4']);
                       }
                       elseif ($dayhrs['1']=='hour' || $dayhrs['1']=='hours' ) {
                        $logistictowarehousemin=($dayhrs['0']*60)+($dayhrs['2']);
                       }
                       else
                       {
                        
                        $logistictowarehousemin=($dayhrs['0']);
                       }
                      
                       // $day=floor($totalhrs/24);
                       // $hrs=$totalhrs%24;

                     // echo 'day=>'.$day.'Hrs==>'.$hrs;
                    }


                 $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$vendorwarehouseaddress123."&destinations=".$address123."&mode=".$personallogistictransport1."&language=us-EN",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "GET",
                      CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "postman-token: aa56e92b-0927-1eae-bfba-614ca1cb50c7"
                      ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                      echo "cURL Error #:" . $err;
                    } else {
                      $distanceres= json_decode($response);


                       $dayhrs1=explode(' ', $distanceres->rows[0]->elements['0']->duration->text);
                      //  echo "<pre>+++";
                      // print_r($dayhrs1);
                      
                      if ($dayhrs1['1']=='day' || $dayhrs1['1']=='days') {
                      $warehousetoshippingmin=($dayhrs1['0']*24*60)+($dayhrs1['2']*60)+($dayhrs1['4']);
                       }
                       elseif ($dayhrs1['1']=='hour' || $dayhrs1['1']=='hours' ) {
                        $warehousetoshippingmin=($dayhrs1['0']*60)+($dayhrs1['2']);
                       }
                       else
                       {
                        
                        $warehousetoshippingmin=($dayhrs1['0']);
                       }
                       
                  $totalmin=$warehousetoshippingmin+$logistictowarehousemin;
                 $totalhrs=number_format(($totalmin/60),2);

                      $day=floor(($totalmin/60)/24);
                      $hrs=$totalhrs%24;
                   $min=$totalmin%60;
                   // echo "min".$min=$totalhrs%24/60;

                      // $day." ".'day'." ".$hrs." ".'Hrs';


                      $personallogisticadminprice = Mage::getModel('personallogistic/personallogisticadmin')->getCollection()->getData();
                      $personallogisticadminprice1=$personallogisticadminprice['0']['min'];
                     $personallogisticadminprice2=$personallogisticadminprice1*$totalhrs;
                     $totalestimateprice=number_format($personallogisticadminprice2+$distancepricetotal,2);
                    $logisticsave = Mage::getModel('personallogistic/personallogistic')->load($data->getPersonallogisticId())
                    ->setTotalestimateprice($totalestimateprice)
                    ->save();
                     
                    }
                      //end here 

           
    $symbol=Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
   
      if ($transportweight>=20) 
       {
        if($i<4)
        {
          
          echo "<input type='radio' name='plusername' style='margin-left:-10px;' onclick='radiocheck(".$data->getPersonallogisticId().','.$data->getTotalestimateprice().");' value='".$data->getPersonallogisticId().",".$data->getTotalestimateprice()."'";

          if($sesPLuserId == $userid){echo "checked";}
       
          echo ">".$data->getFirstname()." ".$data->getLastname()."-".$_weight."kg"."-".$day." ".'day'." ".$hrs." ".'Hrs'." ".$min." ".'Min'." - ".$symbol.$totalestimateprice."<br>";

          Mage::getSingleton('core/session')->unsPluserRadioID();
        }
      }
      $i++;
    }

}
 
}

