<?php
class VES_VendorsPriceComparison2_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SPECIAL_CHAR = '||||';
    
    /**
     * require new product to be approved before showing on website. 
     * @return boolean
     */
    public function newProductApproval(){
        return Mage::getStoreConfig('vendors/pricecomparison2/new_product_approval');
    }
    
    /**
     * Require new product to be approved before showing on website.
     * @return boolean
     */
    public function updateProductApproval(){
        return Mage::getStoreConfig('vendors/pricecomparison2/product_update_approval');
    }
    
    /**
     * Is enabled condition field
     * @return boolean
     */
    public function isEnabledCondition(){
        return Mage::getStoreConfig('vendors/pricecomparison2/enable_condition');
    }
    
    /**
     * Get the with of logo in the price comparison block
     * @return int
     */
    public function getLogoWidth(){
        return 50;
    }
    
    /**
     * Get the height of logo in the price comparison block
     * @return int
     */
    public function getLogoHeight(){
        return 50;
    }
    
    /**
     * Get the description max length in the price comparison block
     * @return number
     */
    public function getDescriptionLength(){
        return 250;
    }
}