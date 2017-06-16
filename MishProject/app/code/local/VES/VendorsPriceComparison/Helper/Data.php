<?php
class VES_VendorsPriceComparison_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getLogoWidth(){
		return Mage::getStoreConfig('vendors/pricecomparison/logo_width');
	}
	
	public function getLogoHeight(){
		return Mage::getStoreConfig('vendors/pricecomparison/logo_height');
	}
	
	public function getDescriptionLength(){
		return Mage::getStoreConfig('vendors/pricecomparison/description_length');
	}
}