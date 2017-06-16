<?php

class VES_VendorsPriceComparison2_Model_Observer
{
    /**
     * If the product is enabled for price comparison set has options to true
     * @param Varien_Event_Observer $observer
     */
	public function catalog_product_load_after(Varien_Event_Observer $observer){
		$product = $observer->getProduct();
		/* $priceComparisonCollection = Mage::getModel('pricecomparison2/pricecomparison')->getCollection()->addFieldToFilter('product_id',$product->getId());
		if($priceComparisonCollection->count()){
		  $product->setHasOptions(true);
		} */
	}
	
	/**
	 * Reset the vendor id for price comparison items
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendors_checkout_init_vendor_id(Varien_Event_Observer $observer){
	    $transport = $observer->getTransport();
	    $item = $transport->getItem();

	    $buyRequest = $item->getBuyRequest();
	    if($item instanceof Mage_Sales_Model_Quote_Item || $item instanceof Mage_Sales_Model_Order_Item){
	        $buyRequest = $buyRequest->getData();
	    }else{
			if($buyRequest){
				   $buyRequest = $buyRequest->getValue();
					$buyRequest = unserialize($buyRequest);
			}
	     
	    }	    

	   
	    if(isset($buyRequest['pricecomparison']) && $buyRequest['pricecomparison']){
	        $priceComparison = Mage::getModel('pricecomparison2/pricecomparison')->load($buyRequest['pricecomparison']);
	        if($priceComparison->getId()){
	            $transport->setVendorId($priceComparison->getVendorId());
	        }
	    }
	}
	
	/**
	 * Add the custom options to the item to separate same item, same sku in shopping cart.
	 * @param Varien_Event_Observer $observer
	 */
	public function catalog_product_type_prepare_full_options(Varien_Event_Observer $observer){
	    $product = $observer->getProduct();
	    $buyRequest    = $observer->getBuyRequest();

        if($buyRequest->getPricecomparison()){
            $product->addCustomOption('related_pricecomparison',$buyRequest->getPricecomparison());
        }else{
            $product->addCustomOption('related_pricecomparison','none');
        }
	}
	
	/**
	 * set the item custom price.
	 * @param Varien_Event_Observer $observer
	 */
	public function sales_quote_item_set_product(Varien_Event_Observer $observer){
	    $quoteItem = $observer->getQuoteItem();
	    $product   = $observer->getProduct();
	    $buyRequest    = $quoteItem->getBuyRequest();
	    
	    if(!$buyRequest->getPricecomparison()) return;
	    $priceComparison = Mage::getModel('pricecomparison2/pricecomparison')->load($buyRequest->getPricecomparison());
	    if(!$priceComparison->getId()) return;
		
	 	$oldSku = $quoteItem->getSku();
		$vendor = Mage::getModel("vendors/vendor")->load($priceComparison->getVendorId());
		$currentVendor = Mage::getModel("vendors/vendor")->load($product->getVendorId());
		//set sku again
		if($product->getVendorId() && $currentVendor->getId()){
			$newSku = preg_replace("/".$currentVendor->getVendorId()."/is", $vendor->getVendorId() ,$oldSku);
		}else{
			$newSku = $vendor->getVendorId()."_".$oldSku;
		}
		$quoteItem->setSku($newSku);
		
	    if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
	        $additionalInfo = $priceComparison->getData('additional_info');
	        $additionalInfo = json_decode($additionalInfo,true);
	        $customPrice = 0;
	        
	        foreach($additionalInfo as $addInfo){
	            $check = true;
    	        foreach($buyRequest->getData('super_attribute') as $attributeId=>$value){
    	            if($addInfo[$attributeId] != $value) $check = false;
    	        }
    	        if($check){
    	            $customPrice = $addInfo['price'];
    	            break;
    	        }
	        }
	        $quoteItem->setOriginalCustomPrice($customPrice);
	        $quoteItem->setCustomPrice($customPrice);
	    }else{
	       $quoteItem->setOriginalCustomPrice($priceComparison->getPrice());
	       $quoteItem->setCustomPrice($priceComparison->getPrice());
	    }
	}
}