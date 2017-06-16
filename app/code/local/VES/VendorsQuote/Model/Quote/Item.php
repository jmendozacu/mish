<?php

class VES_VendorsQuote_Model_Quote_Item extends Mage_Core_Model_Abstract
{
    /**
     * Quote store
     * @var Mage_Core_Model_Store
     */
	protected $_quote;
	
	protected $_requested_proposal;
	
	protected $_proposals;
	
	protected $_usedAttributes = '_cache_instance_used_attributes';
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsquote/quote_item');
    }
    
    /**
     * Processing object before save data
     *
     * @return VES_VendorsQuote_Model_Quote
     */
    protected function _beforeSave()
    {
        if($this->isObjectNew()){
            $this->setData('created_at',Mage::getModel('core/date')->date());
            $this->setData('updated_at',Mage::getModel('core/date')->date());
        }
        return parent::_beforeSave();
    }
    
    /**
     * Processing object before delete data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeDelete()
    {
        /*Delete all proposals relate to this item*/
        $proposalCollection = $this->getProposalCollection();
        foreach($proposalCollection as $proposal){
            $proposal->delete();
        }
        return parent::_beforeDelete();
    }
    
    /**
     * Set Quote
     * @param VES_VendorsQuote_Model_Quote $quote
     */
    public function setQuote(VES_VendorsQuote_Model_Quote $quote){
        $this->_quote = $quote;
        $this->setData('quote_id',$quote->getId());
        return $this;
    }
    
    /**
     * Get quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function getQuote(){
        if(!isset($this->_quote)){
            $this->_quote = Mage::getModel('vendorsquote/quote')->load($this->getQuoteId());
        }
        
        return $this->_quote;
    }
    
    /**
     * Get related product
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct(){
        if(!isset($this->_product)){
            $this->_product = Mage::getModel('catalog/product')->load($this->getProductId());
        }
        return $this->_product;
    }
    
    /**
     * Get product type id
     * @return string
     */
    public function getProductType(){
        return $this->getProduct()->getTypeId();
    }
    
    /**
     * Add proposal for a quote item
     * @param float $qty
     * @param float $price
     * @return VES_VendorsQuote_Model_Quote_Item_Proposal
     */
    public function addProposal($qty, $price = null){
        $proposal = Mage::getModel('vendorsquote/quote_item_proposal');
        $proposal->setData(array(
            'item_id'   => $this->getId(),
            'qty'       => $qty,
            'price'     => $price,
        ));
        
        $proposal->save();
        if(!$this->getDefaultProposal()){$this->setDefaultProposal($proposal->getId())->save();}
        
        return $proposal;
    }
    
    /**
     * Update proposal.
     * @param int $proposalId
     * @param float $qty
     * @param float $price
     * @return VES_VendorsQuote_Model_Quote_Item_Proposal
     */
    public function updateProposal($proposalId,$qty,$price){
        if(!$proposalId) throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The proposal id is required.'));
        $proposal = Mage::getModel('vendorsquote/quote_item_proposal')->load($proposalId);
        $proposal->setPrice($price);
        $proposal->setQty($qty);
        $proposal->save();
        return $proposal;
    }
    
    /**
     * Get requested proposal
     * @return VES_VendorsQuote_Model_Quote_Item_Proposal
     */
    public function getRequestedProposal(){
        if(!isset($this->_requested_proposal)){
            $this->_requested_proposal = Mage::getModel('vendorsquote/quote_item_proposal')->load($this->getDefaultProposal());
        }
        return $this->_requested_proposal;
    }
    
    /**
     * Get default proposal
     * @return VES_VendorsQuote_Model_Quote_Item_Proposal
     */
    public function getDefaultProposalObject(){
        return $this->getRequestedProposal();
    }
    
    /**
     * Get requested qty
     */
    public function getRequestedQty(){
        return $this->getRequestedProposal()->getQty();
    }
    
    /**
     * Update requested qty
     * @param unknown $qty
     * @return VES_VendorsQuote_Model_Quote_Item
     */
    public function updateRequestedQty($qty){
        $this->getRequestedProposal()->setQty($qty)->save();
        return $this;
    }
    
    /**
     * Get proposal collection
     * @return VES_VendorsQuote_Model_Resource_Quote_Item_Proposal_Collection
     */
    public function getProposalCollection(){
        if(!isset($this->_proposals)){
            $this->_proposals = Mage::getModel('vendorsquote/quote_item_proposal')->getCollection()->addFieldToFilter('item_id',$this->getId())->setOrder('qty','ASC');
        }
        
        return $this->_proposals;
    }
    
    /**
     * Get All Proposals
     * @return VES_VendorsQuote_Model_Resource_Quote_Item_Proposal_Collection
     */
    public function getProposals(){
        return $this->getProposalCollection();
    }
    
    /**
     * Retrieve proposal model object by proposal identifier
     *
     * @param   int $proposalId
     * @return  Mage_Sales_Model_Quote_Item_Proposal
     */
    public function getProposalById($proposalId)
    {
        return $this->getProposalCollection()->getItemById($proposalId);
    }
    
    /**
     * Check product representation in item
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   Varien_Object
     * @return  bool
     */
    public function representProduct(Mage_Catalog_Model_Product $product, Varien_Object $request){
        if($this->getProductId() == $product->getId()){
            $buyRequest = json_decode($this->getBuyRequest(),true); 
            /*check custom options*/
            if(isset($buyRequest['options']) && $request->getData('options')){
                $options = $request->getData('options');
                if(!$options || !is_array($options)) return false;
                
                foreach($options as $key=>$value){
                    if(!isset($buyRequest['options'][$key]) || $buyRequest['options'][$key] != $value) return false;
                }
                
                foreach($buyRequest['options'] as $key=>$value){
                    if(!isset($options[$key]) || $options[$key] != $value) return false;
                }
            }elseif(isset($buyRequest['options']) || $request->getData('options')) return false;
            
            /*Check configurable options*/
            if (isset($buyRequest['super_attribute']) && ($data = $buyRequest['super_attribute'])) {
                $newItemData = $request->getData('super_attribute');
                foreach ($data as $attributeId => $attributeValue) {
                    if(!isset($newItemData[$attributeId]) || ($newItemData[$attributeId] != $attributeValue)) return false;
                }
                
                foreach ($newItemData as $attributeId => $attributeValue) {
                    if(!isset($data[$attributeId]) || ($data[$attributeId] != $attributeValue)) return false;
                }
            }
            
            /*Check bundle options*/

            if(isset($buyRequest['bundle_option']) || $request->getData('bundle_option')){
                if(!is_array($buyRequest['bundle_option']) || !is_array($request->getData('bundle_option'))) return false;
                $bundleOption = $request->getData('bundle_option');
                foreach($buyRequest['bundle_option'] as $key=>$value){
                    if(!isset($bundleOption[$key]) || $bundleOption[$key] != $value) return false;
                }
                
                foreach($bundleOption as $key=>$value){
                    if(!isset($buyRequest['bundle_option'][$key]) || $buyRequest['bundle_option'][$key] != $value) return false;
                }
            }
            
            if(isset($buyRequest['bundle_option_qty']) || $request->getData('bundle_option_qty')){
                if(!is_array($buyRequest['bundle_option_qty']) || !is_array($request->getData('bundle_option_qty'))) return false;
                $bundleOption = $request->getData('bundle_option_qty');
                foreach($buyRequest['bundle_option_qty'] as $key=>$value){
                    if(!isset($bundleOption[$key]) || $bundleOption[$key] != $value) return false;
                }
            
                foreach($bundleOption as $key=>$value){
                    if(!isset($buyRequest['bundle_option_qty'][$key]) || $buyRequest['bundle_option_qty'][$key] != $value) return false;
                }
            }
            return true;
        }
        return false;
    }
    
    /**
     * Compare current item with an quote item
     * @param VES_VendorsQuote_Model_Quote_Item $item
     * @return boolean
     */
    public function compare(VES_VendorsQuote_Model_Quote_Item $item){
        $buyRequest     = json_decode($this->getBuyRequest(),true);
        $itemBuyRequest = json_decode($item->getBuyRequest(),true);
        /*check custom options*/
        if(isset($buyRequest['options']) && isset($itemBuyRequest['options'])){
            $options = $itemBuyRequest['options'];
            if(!$options || !is_array($options)) return false;
        
            foreach($options as $key=>$value){
                if(!isset($buyRequest['options'][$key]) || $buyRequest['options'][$key] != $value) return false;
            }
        
            foreach($buyRequest['options'] as $key=>$value){
                if(!isset($options[$key]) || $options[$key] != $value) return false;
            }
        }elseif(isset($buyRequest['options']) || $itemBuyRequest['options']) return false;
        
        /*Check configurable options*/
        if (isset($buyRequest['super_attribute']) && ($data = $buyRequest['super_attribute'])) {
            if(!isset($itemBuyRequest['super_attribute'])) return false;
            
            $newItemData = $itemBuyRequest['super_attribute'];
            foreach ($data as $attributeId => $attributeValue) {
                if(!isset($newItemData[$attributeId]) || ($newItemData[$attributeId] != $attributeValue)) return false;
            }
        
            foreach ($newItemData as $attributeId => $attributeValue) {
                if(!isset($data[$attributeId]) || ($data[$attributeId] != $attributeValue)) return false;
            }
        }
        /*Check bundle options*/
        
        if(isset($buyRequest['bundle_option']) || isset($itemBuyRequest['bundle_option'])){
            if(!is_array($buyRequest['bundle_option']) || !is_array($itemBuyRequest['bundle_option'])) return false;
            $bundleOption = $itemBuyRequest['bundle_option'];
            foreach($buyRequest['bundle_option'] as $key=>$value){
                if(!isset($bundleOption[$key]) || $bundleOption[$key] != $value) return false;
            }
        
            foreach($bundleOption as $key=>$value){
                if(!isset($buyRequest['bundle_option'][$key]) || $buyRequest['bundle_option'][$key] != $value) return false;
            }
        }
        
        if(isset($buyRequest['bundle_option_qty']) || $itemBuyRequest['bundle_option_qty']){
            if(!is_array($buyRequest['bundle_option_qty']) || !is_array($itemBuyRequest['bundle_option_qty'])) return false;
            $bundleOption = $itemBuyRequest['bundle_option_qty'];
            foreach($buyRequest['bundle_option_qty'] as $key=>$value){
                if(!isset($bundleOption[$key]) || $bundleOption[$key] != $value) return false;
            }
        
            foreach($bundleOption as $key=>$value){
                if(!isset($buyRequest['bundle_option_qty'][$key]) || $buyRequest['bundle_option_qty'][$key] != $value) return false;
            }
        }
        return true;
    }
    
    
    /**
     * Calculate item default price
     */
    public function calculatePrice(){
        $product = $this->getProduct();
        $buyRequest = json_decode($this->getBuyRequest(),true);
        $additionalPrice = 0;
        
        /*item has custom options*/
        if(isset($buyRequest['options'])){
            /*Have custom options*/
            $optionIds = array_keys($buyRequest['options']);
            foreach ($optionIds as $optionId) {
                $option = $product->getOptionById($optionId);
                if(!$option) continue;
                $itemOption = $buyRequest['options'][$optionId];
                if(is_array($itemOption)) $itemOption = implode(",", $itemOption);
                if(!$itemOption) continue;
                $group = $option->groupFactory($option->getType())
                ->setOption($option)
                ->setConfigurationItem($this)
                ->setConfigurationItemOption($itemOption);
                
                $price = $group->getOptionPrice($itemOption);
                $additionalPrice += $price;
            }
        }
        
        /*Configurable item*/
        if (isset($buyRequest['super_attribute']) && ($data = $buyRequest['super_attribute'])) {
            $typeInstance = $product->getTypeInstance(true);
            $usedProductAttributeIds = $typeInstance->getUsedProductAttributeIds($product);
            $usedAttributes = $typeInstance->getProduct($product)->getData($this->_usedAttributes);
            foreach ($data as $attributeId => $attributeValue) {
                if (isset($usedAttributes[$attributeId])) {
                    $attribute = $usedAttributes[$attributeId];
                    $prices = $attribute->getPrices();
                    foreach($prices as $value){
                        if($value['value_index'] == $attributeValue){
                            $additionalPrice += isset($value['pricing_value'])?$value['pricing_value']:0;
                            break;
                        }
                    }                   
                    
                }
            }
        }
        
        /*Bundle item*/
        
        if(isset($buyRequest['bundle_option'])){
            $bundleOptions = $buyRequest['bundle_option'];
            $typeInstance = $product->getTypeInstance(true);
            $bundleOptionsIds = array_keys($bundleOptions);
            $bundleOptionQty = $buyRequest['bundle_option_qty'];
            
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            $bundleSelectionIds = array_values($bundleOptions);
            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds(
                    $bundleSelectionIds,
                    $product
                );
                $bundleOpts = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOpts as $bundleOption) {
                    if ($bundleSelections = $bundleOption->getSelections()) {
                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = (isset($bundleOptionQty[$bundleOption->getId()])?$bundleOptionQty[$bundleOption->getId()]:$bundleSelection->getSelectionQty()) * 1;
                            if ($qty) {
                                $price = $product->getPriceModel()->getSelectionFinalTotalPrice($product,$bundleSelection,$this->getRequestedQty() * 1,$qty,false,true);
                                $additionalPrice += ($qty*$price);
                            }
                        }
                    }
                }
            }
        }
        
        
        $this->setPrice($product->getFinalPrice()+$additionalPrice);
        $this->getRequestedProposal()->setPrice($product->getFinalPrice()+$additionalPrice)->save();
        return $this;
    }
}