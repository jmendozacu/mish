<?php
class VES_VendorsPriceComparison2_Block_Vendor_Selectandsell_Edit_Form_Element_Option_Table extends Mage_Core_Block_Template
{
    protected $_child_products;
    
    protected $_used_attributes;
    
	public function __construct()
	{
	    $this->setTemplate('ves_vendorspricecomparison2/selectandsell/options.phtml');
		return parent::__construct();
	}
	
	/**
	 * Get child products relate to the current configurable products.
	 */
	public function getChildProducts(){
	    if(!$this->_child_products){
	        $product = $this->getProduct();
	        $this->_child_products = $product->getTypeInstance()->getUsedProducts();
	    }
	    return $this->_child_products;
	    
	    $product = $this->getProduct();
	    $usedAttributes = $product->getTypeInstance()->getUsedProductAttributes();
	}
	
	/**
	 * Get used attributes of the current configurable products.
	 */
	public function getUsedAttributes(){
	    if(!$this->_used_attributes){
	        $product = $this->getProduct();
	        $this->_used_attributes = $product->getTypeInstance()->getUsedProductAttributes();
	    }
	    return $this->_used_attributes;
	}
	
	/**
	 * Get Additional info
	 * @return mixed|multitype:
	 */
	public function getAdditionalInfo(){
	    if($priceComparison = $this->getPriceComparison()){
	        return json_decode($priceComparison->getAdditionalInfo(),true);
	    }
	    
	    return array();
	    
	}
}