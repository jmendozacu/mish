<?php
class VES_VendorsSelectAndSell_Block_Product_Edit extends Mage_Core_Block_Template{
	public function getProduct(){
		return Mage::registry('product');
	}
	
	public function getSaveProductUrl(){
		return $this->getUrl('vendors/selectandsell/save',array('pricecomparison'=>$this->getProduct()->getId()));
	}
}
