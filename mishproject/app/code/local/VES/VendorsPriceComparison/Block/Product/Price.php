<?php
class VES_VendorsPriceComparison_Block_Product_Price extends Mage_Catalog_Block_Product_Abstract{
	protected $_product;
	
	public function setProduct($product){
		$this->_product = $product;
	}
	
	public function getProduct(){
		return $this->_product;
	}
}
