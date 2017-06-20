<?php
class VES_VendorsRma_Block_Email_Item extends VES_VendorsRma_Block_Customer_Abstract
{
	
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getRequestRma(){
    	if(!$this->getData('rma_request')){
    		$this->setData('rma_request',Mage::registry('request'));
    	}
    	return $this->getData('rma_request');
    }
    
    public function getItems(){
        $items = Mage::getModel('vendorsrma/item')->getCollection()->addFieldToFilter('request_id',$this->getRequestRma()->getId());
        return $items;
    }

    public function getItemName($itemId){
        $sale = Mage::getModel("sales/order_item")->load($itemId);
        return $sale->getName();
    }
}