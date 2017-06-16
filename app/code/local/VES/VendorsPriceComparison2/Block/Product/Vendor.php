<?php
class VES_VendorsPriceComparison2_Block_Product_Vendor extends VES_VendorsPriceComparison2_Block_Product_Default{
	
    public function getLogoWidth(){
    	return Mage::helper('pricecomparison2')->getLogoWidth();
    }
    
    public function getLogoHeight(){
    	return Mage::helper('pricecomparison2')->getLogoHeight();
    }

    /**
     * Get image url based on file path
     * @param string $file
     */
	public function getImageUrl($file){
    	echo Mage::helper('pricecomparison2/image')->init($file)->resize($this->getLogoWidth(),$this->getLogoHeight());
    }
    
    /**
     * Check if the vendor home page is enabled or not.
     * @return boolean
     */
    public function isEnabledVendorHomePage(){
        return Mage::helper('core')->isModuleEnabled('VES_VendorsPage');
    }
    
    /**
     * Get Vendor hompage page URL
     * @param VES_Vendors_Model_Vendor $vendor
     */
    public function getVendorPage(VES_Vendors_Model_Vendor $vendor){
        return Mage::helper('vendorspage')->getUrl($vendor);
    }
}
