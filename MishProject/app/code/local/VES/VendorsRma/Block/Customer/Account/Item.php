<?php
class VES_VendorsRma_Block_Customer_Account_Item extends VES_VendorsRma_Block_Customer_Abstract
{
	
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getItems(){
        $items = Mage::getModel('vendorsrma/item')->getCollection()->addFieldToFilter('request_id',$this->getRequestRma()->getId());
        return $items;
    }

    public function getItemName($itemId){
        $sale = Mage::getModel("sales/order_item")->load($itemId);
        return $sale->getName();
    }

    public function getItemImage($itemId){
        $sale = Mage::getModel("sales/order_item")->load($itemId);
        return Mage::helper('catalog/image')->init($sale->getProduct(), 'thumbnail')->resize(70);;
    }
}