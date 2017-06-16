<?php
class VES_VendorsPriceComparison_Block_Product_Ajax extends VES_VendorsPriceComparison_Block_Product{
	protected function _prepareLayout(){
		Mage::dispatchEvent('ves_vendor_pricecomparison_prepare_columns',array('block'=>$this));
		return Mage_Catalog_Block_Product_Abstract::_prepareLayout();
	}
}