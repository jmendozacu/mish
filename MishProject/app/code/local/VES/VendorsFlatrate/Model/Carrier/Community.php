<?php

class VES_VendorsFlatrate_Model_Carrier_Community
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'vendor_flatrate';
    protected $_isFixed = true;

    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @param   string $vendor_id
     * @return  mixed
     */
    public function getVendorConfigData($field, $vendorId)
    {
        /**
         * field = {free_shipping_subtotal; price; type}
         */
        $path = 'shipping/flatrate/'.$field;
        return Mage::helper('vendorsconfig')->getVendorConfig($path,$vendorId);
    }

    public function getMode() {
        return Mage::helper('vendors/vendor')->getMode();
    }


    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');
        

        $quotes = array();
        $vendorRates = array();
        /*loop through all items to get vendor id of each item*/

        /*
         * seperate each vendor item to dependence array in quotes array.
         */
        if($request->getAllItems()) {
            foreach($request->getAllItems() as $item) {
            	if($item->getParentItem()) continue;
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
                if($product->getVendorId()) {
					if($item->getVendorId()){
						$vendorId = $item->getVendorId();
					}else{
						$vendorId = $product->getVendorId();

					}
					$transport = new Varien_Object(array('vendor_id'=>$vendorId,'item'=>$item));
					Mage::dispatchEvent('ves_vendors_checkout_init_vendor_id',array('transport'=>$transport));
					$vendorId = $transport->getVendorId();
					
                	/*Get all flatrate shipping info*/
                	if(!isset($vendorRates[$vendorId])){
                		$vendorRates[$vendorId] = array();
                		$rates = $this->getVendorConfigData('rates', $vendorId);
                		if($rates){
                			$rates = unserialize($rates);
                			foreach($rates as $rate){
                				$vendorRates[$vendorId][$rate['identifier']] = array(
                					'title'	=> $rate['title'],
                					'price'	=> $rate['price'],
                					'type'	=> $rate['type'],
                					'free_shipping_subtotal'	=> $rate['free_shipping_subtotal'],
                				);
                			}
                		}
                	}
                	
                	/*Get item by vendor id*/
                	if(!isset($quotes[$vendorId])) $quotes[$vendorId] = array();
                    $quotes[$vendorId][] = $item;
                } else {
                    $quotes['no_vendor'][] = $item;
                }
            }
            
            ksort($vendorRates);
            
            /*Add shipping method for each vendor flatrate*/
            foreach($vendorRates as $vendorId=>$rates){
                if(!$this->getVendorConfigData('active', $vendorId)) continue;
            	$total	= 0;
            	foreach($quotes[$vendorId] as $item){
                	if($item->getParentItem()) continue;
                	$total += $item->getRowTotal();
            	}

		        foreach($rates as $code=>$rate){
		        	$method = Mage::getModel('shipping/rate_result_method');
		        	$method->setVendorId($vendorId);
			        $method->setCarrier('vendor_flatrate');
			        $method->setCarrierTitle($this->getConfigData('title'));
			        
		        	$method->setMethod($code.VES_VendorsShipping_Model_Shipping::DELEMITER.$vendorId);
		       		$method->setMethodTitle($rate['title']);
		       		
			        if($rate['type'] == 'O')    {   //per order
	                    $shippingPrice = $rate['price'];
	                }else{
	                	$shippingPrice 	= 0;
	                    $qty 			= 0;
		                foreach($quotes[$vendorId] as $item){
		                    if($item->getProduct()->isVirtual() && $item->getParentItem()) {
	                            continue;
	                        }
	                        if($item->getFreeShipping()) continue;
	                        $qty+= $item->getQty();
		                    $shippingPrice = $qty * $rate['price'];
			       		}
	                }
		       		
	                if($rate['free_shipping_subtotal'] && $total >= $rate['free_shipping_subtotal']){
	                	$shippingPrice = 0;
	                }
	                $vendorRates[$vendorId][$code]['shipping_price'] = $shippingPrice;
		       		$method->setPrice($shippingPrice);
		            $method->setCost($shippingPrice);
					$result->append($method);
		        }
            }
            
            
        }
        return $result;
    }


    public function updateShippingPrice($quote) {
        $total = 0;
        $_isFreeShipping = false;
        foreach($quote as $item) {
            if($item->getProduct()->isVirtual() || $item->getParentItem()) {
                continue;
            }
            if($item->getFreeShipping()) continue;
            $total += $item->getRowTotal()*$item->getQty();
        }

        if($total >= $this->getFreeShippingSubtotal() && $this->getFreeShippingSubtotal() != '') $_isFreeShipping = true;
        return $_isFreeShipping;
    }

    public function getAllowedMethods()
    {
        return array('vendor_flatrate'=>$this->getConfigData('name'));
    }
}
