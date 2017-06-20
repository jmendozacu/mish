<?php
class VES_VendorsPriceComparison2_Block_Product extends Mage_Catalog_Block_Product_Abstract{
	protected $_columns;

    protected function _prepareLayout(){

		parent::_prepareLayout();
		if($this->canShowVendorProducts()){
		    $headBlock = $this->getLayout()->getBlock('head');
		    if($headBlock){
		        $headBlock->addCss('ves_vendors/pricecomparison2/styles.css');
		        $headBlock->addJs('ves_vendors/pricecomparison2/js.js');
		    }
		}
		
		return $this;
	}
	
	protected function _toHtml(){
	    return $this->canShowVendorProducts()?parent::_toHtml():'';
	}
	
	/**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $displayMinimalPrice
     * @param string $idSuffix
     * @return string
     */

    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $type_id = $product->getTypeId();
        if (Mage::helper('catalog')->canApplyMsrp($product)) {
            $realPriceHtml = $this->_preparePriceRenderer($type_id)
                ->setProduct($product)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
                ->toHtml();
            $product->setAddToCartUrl($this->getAddToCartUrl($product));
            $product->setRealPriceHtml($realPriceHtml);
            $type_id = $this->_mapRenderer;
        }

        return $this->_preparePriceRenderer($type_id)
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }
    
	protected function _cmpOrder($a,$b){
		$aOrder = $a['sort_order']?$a['sort_order']:1000;
		$bOrder = $b['sort_order']?$b['sort_order']:1000;
		return  $aOrder > $bOrder;
	}
	
	public function addColumn($name,$title,$block, $sortorder,$width='',$sortable=false){
		$this->_columns[] = array(
			'name'	=> $name,
			'title'	=> $title,
			'block'	=> $block,
		    'width' => $width,
		    'sort_order'  => $sortorder,
		    'sortable'    => $sortable,
		);
		if(sizeof($this->_columns) >1)
		usort($this->_columns, array($this,"_cmpOrder"));
	}
	
	public function getColumns(){
		return $this->_columns;
	}
	
	/**
	 * Get Column Html
	 * @param unknown $block
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @param VES_VendorsPriceComparison2_Model_Pricecomparison $priceComparison
	 * @return string
	 */
	public function getColumnHtml($block, VES_Vendors_Model_Vendor $vendor, VES_VendorsPriceComparison2_Model_Pricecomparison $priceComparison){
	    $product = $priceComparison->getProduct();
		if(is_string($block)){
			$block = $this->getLayout()->getBlock($block);
			$block->setVendor($vendor)->setPriceComparison($priceComparison)->setProduct($product);
			return $block->toHtml();
		}elseif($block instanceof Mage_Core_Block_Abstract){
			$block->setVendor($vendor)->setPriceComparison($priceComparison)->setProduct($product);
			return $block->toHtml();
		}
		return '';
	}
	/**
	 * Get column block.
	 * @param unknown $block
	 * @param VES_Vendors_Model_Vendor $vendor
	 * @param VES_VendorsPriceComparison2_Model_Pricecomparison $priceComparison
	 * @return Ambigous <Mage_Core_Block_Abstract, boolean, multitype:>|Mage_Core_Block_Abstract
	 */
	public function getColumnBlock($block, VES_Vendors_Model_Vendor $vendor, VES_VendorsPriceComparison2_Model_Pricecomparison $priceComparison){
	    $product = $priceComparison->getProduct();
	    if(is_string($block)){
	        $block = $this->getLayout()->getBlock($block);
	        $block->setVendor($vendor)->setPriceComparison($priceComparison)->setProduct($product);
	        return $block;
	    }elseif($block instanceof Mage_Core_Block_Abstract){
	        $block->setVendor($vendor)->setPriceComparison($priceComparison)->setProduct($product);
	        return $block;
	    }
	    return $this->getLayout()->createBlock('core/template')->setVendor($vendor)->setPriceComparison($priceComparison)->setProduct($product);
	}
	
	/**
	 * Can show price comparison block
	 * @return boolean
	 */
	public function canShowVendorProducts(){
		return !Mage::registry('vendor') && $this->getPriceComparisonCollection()->count()>0;
	}
	
	/**
	 * Get Current Product
	 * @return Mage_Catalog_Model_Product
	 */

	public function getProduct(){

        if(!$this->hasData("current_product")) {
            if(Mage::registry('product'))
            $this->setData("current_product",Mage::registry('product'));
            else{
                $id =  $this->getRequest()->getParam("product");

                $product = Mage::getModel("catalog/product")->load($id);
                $this->setData("current_product",$product);
            }
        }
        return $this->getData("current_product");
	}

	/**
	 * Get price comparison collection
	 * @return VES_VendorsPriceComparison2_Model_Resource_Pricecomparison_Collection
	 */
	public function getPriceComparisonCollection(){
	    if(!$this->getData('price_comparison_collection')){
    	    $productCollection = Mage::getModel('pricecomparison2/pricecomparison')->getCollection()
    	    ->addFieldToFilter('product_id',$this->getProduct()->getId())
    	    ->addFieldToFilter('status',VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_APPROVED);

    	    $this->setData('price_comparison_collection',$productCollection);
	    }
	    return $this->getData('price_comparison_collection');
	}
    
    /**
     * Get price comparison products.
     */
	public function getVendorRelatedProducts(){
		if(!$this->getData('vendor_related_products')){
			$productCollection = $this->getPriceComparisonCollection();
    		$currentProduct = $this->getProduct();
    		if($currentProduct->getVendorId()){
    		  $ownerItem = Mage::getModel('pricecomparison2/pricecomparison')->setData(array(
    		      'product_id' => $currentProduct->getId(),
    		      'vendor_id' => $currentProduct->getVendorId(),
    		      'price' => $currentProduct->getFinalPrice(),
    		      'qty' => $currentProduct->getQty(),
    		      'status' => VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_APPROVED,
		      ));
    		  $productCollection->addItem($ownerItem);
            }
            $this->setData('vendor_related_products',$productCollection);
		}
		return $this->getData('vendor_related_products');
	}
    
	/**
	 * Get Image URL
	 * @param unknown $file
	 */
    public function getImageUrl($file){
    	echo Mage::getBaseUrl('media').$file;
    }
    
    /**
     * Get vendor attributes for the filter
     * @return multitype:
     */
    public function getVendorAttributeForFilter(){
        return explode(',',Mage::getStoreConfig('vendors/pricecomparison2/vendor_filter'));
    }
    
    
    /**
     * Get vendor data which is used for filter.
     * @param VES_Vendors_Model_Vendor $vendor
     * @return array
     */
    public function getVendorDataForFilter(VES_Vendors_Model_Vendor $vendor){
        $data = array();
        foreach($this->getVendorAttributeForFilter() as $attributeCode){
            $data[$attributeCode] = $vendor->getData($attributeCode);
        }
        
        return $data;
    }
    
    /**
     * Get number of products will be displayed
     * @return int
     */
    public function getNumberOfShowingProducts(){
        $number = Mage::getStoreConfig('vendors/pricecomparison2/showing_number');
        return $number?$number:0;
    }
}
