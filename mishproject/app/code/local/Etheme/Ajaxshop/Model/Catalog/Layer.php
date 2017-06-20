<?php

/**
 * Catalog view layer model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Mrugesh Mistry <support@magehouse.com>
 */
class Etheme_Ajaxshop_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{
   
   
	/*
	* Add Filter in product Collection for new price
	*
	* @return object
	*/
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getProductCollection();
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
		
		$this->currentRate = Mage::app()->getStore()->getCurrentCurrencyRate();;
		$max=$this->getMaxPriceFilter();
		$min=$this->getMinPriceFilter();
		
		if($min && $max){
			$collection->getSelect()->where(' final_price >= "'.$min.'" AND final_price <= "'.$max.'" ');
		}
		
        return $collection;
    }
	
	
	/*
	* convert Price as per currency
	*
	* @return currency
	*/
	public function getMaxPriceFilter(){
        if(isset($_GET['max'])){
            $max=$_GET['max'];
            return round($max/$this->currentRate);
        } else return 0;

	}
	
	
	/*
	* Convert Min Price to current currency
	*
	* @return currency
	*/
	public function getMinPriceFilter(){
        if(isset($_GET['min'])){
            $min=$_GET['min'];
            return round($min/$this->currentRate);
        } else return 0;

	}
    
	
}
