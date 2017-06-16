<?php
class VES_VendorsPriceComparison_Block_Product_Vendor extends Mage_Catalog_Block_Product_Abstract{
	protected $_product;
	/**
	 * Set product 
	 */
	public function setProduct($product){
		$this->_product = $product;
	}
	/**
	 * Get Product
	 * @see Mage_Catalog_Block_Product_Abstract::getProduct()
	 */
	public function getProduct(){
		return $this->_product;
	}
	
	/**
	 * Get vendor product url
	 * @param Mage_Catalog_Model_Product $product
	 */
	public function getVendorProductUrl(Mage_Catalog_Model_Product $product){
	    return Mage::helper('core')->isModuleEnabled('VES_VendorsPage')?
	       Mage::getModel('catalog/product_url')->getUrl($product,array('_vendor'=>true)):
	       false;
    }
    
    public function getLogoWidth(){
    	return Mage::helper('pricecomparison')->getLogoWidth();
    }
    
    public function getLogoHeight(){
    	return Mage::helper('pricecomparison')->getLogoHeight();
    }
	public function getDescriptionLength(){
    	return Mage::helper('pricecomparison')->getDescriptionLength();
    }

    /**
     * Get image url based on file path
     * @param string $file
     */
	public function getImageUrl($file){
    	echo Mage::helper('pricecomparison/image')->init($file)->resize($this->getLogoWidth(),$this->getLogoHeight());
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
