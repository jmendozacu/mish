<?php
class VES_VendorsPriceComparison2_Block_Product_Price extends VES_VendorsPriceComparison2_Block_Product_Default{
    /**
     * Get Price html
     * @param VES_VendorsPriceComparison2_Model_Pricecomparison $priceComparison
     */
	public function getPriceHtml($priceComparison){
	    return Mage::helper('core')->currency($priceComparison->getPrice());
	}
	
	public function getValueForSorting(){
	    return $this->getPriceComparison()->getPrice();
	}
}
