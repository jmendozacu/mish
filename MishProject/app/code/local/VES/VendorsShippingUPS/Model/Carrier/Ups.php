<?php

class VES_VendorsShippingUPS_Model_Carrier_Ups extends Mage_Usa_Model_Shipping_Carrier_Ups{
    public function groupItemsByVendor(){
        $quotes = array();
        foreach($this->_request->getAllItems() as $item) {
            if($item->getParentItem()) continue;
            if($item->getProduct()->getVendorId()) {
				
				if($item->getVendorId()){
					$vendorId = $item->getVendorId();
				}else{
					$vendorId = $item->getProduct()->getVendorId();
				}
				$transport = new Varien_Object(array('vendor_id'=>$vendorId,'item'=>$item));
				Mage::dispatchEvent('ves_vendors_checkout_init_vendor_id',array('transport'=>$transport));
				$vendorId = $transport->getVendorId();
				
                /*Get item by vendor id*/
                if(!isset($quotes[$vendorId])) $quotes[$vendorId] = array();
                $quotes[$vendorId][] = $item;
            } else {
                $quotes['no_vendor'][] = $item;
            }
        }
        return $quotes;
    }
    /**
     * Get cgi rates
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getCgiQuotes()
    {
        $quotes = $this->groupItemsByVendor();
        $this->_result = Mage::getModel('shipping/rate_result');
        foreach($quotes as $vendorId=>$items){
            if(!Mage::helper('vendorsconfig')->getVendorConfig('shipping/ups/active',$vendorId)) continue;
            
            /*================================================*/
            $vendor = Mage::getModel('vendors/vendor')->load($vendorId);
            $r = $this->_rawRequest;
            
            $vendorCountry  = $vendor->getCountryId();
            $vendorCountry  = Mage::getModel('directory/country')->load($vendorCountry)->getIso2Code();
            $vendorPostcode = $vendor->getPostcode();
            $vendorCity     = $vendor->getCity();
            
            $weight = 0;
            foreach($items as $item){
                $weight+= $item->getRowWeight();
            }
            $weight = $this->getTotalNumOfBoxes($weight);
            $weight = $this->_getCorrectWeight($weight);
            /*================================================*/
            
            $params = array(
                'accept_UPS_license_agreement' => 'yes',
                '10_action'      => $r->getAction(),
                '13_product'     => $r->getProduct(),
                '14_origCountry' => $vendorCountry,     /*Vendor Country*/
                '15_origPostal'  => $vendorPostcode,    /*Vendor Postcode*/
                'origCity'       => $vendorCity,        /*Vendor City*/
                '19_destPostal'  => Mage_Usa_Model_Shipping_Carrier_Abstract::USA_COUNTRY_ID == $r->getDestCountry() ?
                substr($r->getDestPostal(), 0, 5) :
                $r->getDestPostal(),
                '22_destCountry' => $r->getDestCountry(),
                '23_weight'      => $weight,
                '47_rate_chart'  => $r->getPickup(),
                '48_container'   => $r->getContainer(),
                '49_residential' => $r->getDestType(),
                'weight_std'     => strtolower($r->getUnitMeasure()),
            );
            $params['47_rate_chart'] = $params['47_rate_chart']['label'];
       
            $responseBody = $this->_getCachedQuotes($params);
            if ($responseBody === null) {
                $debugData = array('request' => $params);
                try {
                    $url = $this->getConfigData('gateway_url');
                    if (!$url) {
                        $url = $this->_defaultCgiGatewayUrl;
                    }
                    $client = new Zend_Http_Client();
                    $client->setUri($url);
                    $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
                    $client->setParameterGet($params);
                    $response = $client->request();
                    $responseBody = $response->getBody();
        
                    $debugData['result'] = $responseBody;
                    $this->_setCachedQuotes($params, $responseBody);
                }
                catch (Exception $e) {
                    $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                    $responseBody = '';
                }
                $this->_debug($debugData);
            }
            /*================================================*/
            $this->_parseCgiResponseNew($responseBody,$vendor);
            /*================================================*/
        }
        return $this->_result;
    }
    
    
    
    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _parseCgiResponseNew($response,VES_Vendors_Model_Vendor $vendor)
    {
        $costArr = array();
        $priceArr = array();
        $errorTitle = Mage::helper('usa')->__('Unknown error');
        if (strlen(trim($response))>0) {
            $rRows = explode("\n", $response);
            $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
            foreach ($rRows as $rRow) {
                $r = explode('%', $rRow);
                switch (substr($r[0],-1)) {
                    case 3: case 4:
                        if (in_array($r[1], $allowedMethods)) {
                            $responsePrice = Mage::app()->getLocale()->getNumber($r[10]);
                            $costArr[$r[1]] = $responsePrice;
                            $priceArr[$r[1]] = $this->getMethodPrice($responsePrice, $r[1]);
                        }
                        break;
                    case 5:
                        $errorTitle = $r[1];
                        break;
                    case 6:
                        if (in_array($r[3], $allowedMethods)) {
                            $responsePrice = Mage::app()->getLocale()->getNumber($r[10]);
                            $costArr[$r[3]] = $responsePrice;
                            $priceArr[$r[3]] = $this->getMethodPrice($responsePrice, $r[3]);
                        }
                        break;
                }
            }
            asort($priceArr);
        }
    
        $result = $this->_result;
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setVendorId($vendor->getId());
                $rate->setCarrier('ups');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method.VES_VendorsShipping_Model_Shipping::DELEMITER.$vendor->getId()); /*This is important*/
                $method_arr = $this->getCode('method', $method);
                $rate->setMethodTitle($method_arr);
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
    
        return $this;
    }
}
