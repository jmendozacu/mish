<?php

class VES_VendorsPriceComparison2_Model_Observer_Inventory extends Mage_CatalogInventory_Model_Observer
{
    /**
     * Subtract quote items qtys from stock items related with quote items products.
     *
     * Used before order placing to make order save/place transaction smaller
     * Also called after every successful order placement to ensure subtraction of inventory
     *
     * @param Varien_Event_Observer $observer
     */
    public function subtractQuoteInventory(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
    
        // Maybe we've already processed this quote in some event during order placement
        // e.g. call in event 'sales_model_service_quote_submit_before' and later in 'checkout_submit_all_after'
        if ($quote->getInventoryProcessed()) {
            return;
        }
        
        $priceComparisonItems = array();
        $generalItems = array();
        foreach($quote->getAllItems() as $item){
            if($item->getParentItemId() && in_array($item->getParentItemId(), $priceComparisonItems)) continue;
            $buyRequest    = $item->getBuyRequest();
            if($buyRequest->getPricecomparison()){
                $priceComparisonItems[] = $item;
            }else{
                $generalItems[] = $item;
            }
        }

        $this->_registerProductSale($priceComparisonItems);

        $items = $this->_getProductsQty($generalItems);
    
        /**
         * Remember items
        */
        $this->_itemsForReindex = Mage::getSingleton('cataloginventory/stock')->registerProductsSale($items);
    
        $quote->setInventoryProcessed(true);
        return $this;
    }
    
    protected function _registerProductSale($items){
        foreach($items as $item){
            $buyRequest         = $item->getBuyRequest();
            $priceComparison    = Mage::getModel('pricecomparison2/pricecomparison')->load($buyRequest->getPricecomparison());
            if(!$priceComparison->getId()) return;
            
            $product = $item->getProduct();
            if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
                $additionalInfo = $priceComparison->getData('additional_info');
                $additionalInfo = json_decode($additionalInfo,true);      
                           
                foreach($additionalInfo as $key=>$addInfo){
                    $check = true;
                    foreach($buyRequest->getData('super_attribute') as $attributeId=>$value){
                        if($addInfo[$attributeId] != $value) $check = false;
                    }
                    if($check){
                        $addInfo['qty'] -= $item->getTotalQty();
                        $additionalInfo[$key] = $addInfo;
                        break;
                    }
                }
                $priceComparison->setData('additional_info',json_encode($additionalInfo))->save();
                
            }else{
                $priceComparison->setQty($priceComparison->getQty() - $item->getTotalQty())->save();
            }
            
        }
    }
    
    
    /**
     * Revert quote items inventory data (cover not success order place case)
     * @param $observer
     */
    public function revertQuoteInventory($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $items = $this->_getProductsQty($quote->getAllItems());
        
        $priceComparisonItems = array();
        $items = array();
        foreach($quote->getAllItems() as $item){
            if($item->getParentItemId() && in_array($item->getParentItemId(), $priceComparisonItems)) continue;
            $buyRequest    = $item->getBuyRequest();
            if($buyRequest->getPricecomparison()){
                $priceComparisonItems[] = $item;
            }else{
                $items[] = $item;
            }
        }
        $this->_revertProductsSale($priceComparisonItems);
        
        Mage::getSingleton('cataloginventory/stock')->revertProductsSale($items);
    
        // Clear flag, so if order placement retried again with success - it will be processed
        $quote->setInventoryProcessed(false);
    }
    
    protected function _revertProductsSale($items){
        foreach($items as $item){
            $buyRequest         = $item->getBuyRequest();
            $priceComparison    = Mage::getModel('pricecomparison2/pricecomparison')->load($buyRequest->getPricecomparison());
            if(!$priceComparison->getId()) return;
    
            $product = $item->getProduct();
            if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
                $additionalInfo = $priceComparison->getData('additional_info');
                $additionalInfo = json_decode($additionalInfo,true);
                 
                foreach($additionalInfo as $key=>$addInfo){
                    $check = true;
                    foreach($buyRequest->getData('super_attribute') as $attributeId=>$value){
                        if($addInfo[$attributeId] != $value) $check = false;
                    }
                    if($check){
                        $addInfo['qty'] += $item->getTotalQty();
                        $additionalInfo[$key] = $addInfo;
                        break;
                    }
                }
                $priceComparison->setData('additional_info',json_encode($additionalInfo))->save();
    
            }else{
                $priceComparison->setQty($priceComparison->getQty() + $item->getTotalQty())->save();
            }
    
        }
    }
}