<?php
class VES_VendorsPriceComparison2_Block_Product_Description extends VES_VendorsPriceComparison2_Block_Product_Default{
	/**
	 * Get Description Max Length
	 */
    public function getDescriptionLength(){
	    return Mage::helper('pricecomparison2')->getDescriptionLength();
	}
	
	/**
	 * Cut the description to fit with description length
	 * @param string $description
	 */
	public function formatDescription($description){
	    if(strlen($description) > $this->getDescriptionLength())
	        return substr($description,0,strrpos(substr($description,0,$this->getDescriptionLength()),' ')).'...';
	     
	    return $description;
	}
}
