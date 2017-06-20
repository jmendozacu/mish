<?php
class VES_VendorsPriceComparison_Block_Product extends Mage_Catalog_Block_Product_Abstract{
	protected $_columns;
	
	protected function _prepareLayout(){
		parent::_prepareLayout();
		if($this->canShowVendorProducts()){
			$productInfo = $this->getLayout()->getBlock('product.info');
			$this->getLayout()->getBlock('product.info.options.wrapper.bottom')->setTemplate('');
			$productInfo->unsetChild('product_type_data');
			$headBlock = $this->getLayout()->getBlock('head');
			if($headBlock){
				$headBlock->addCss('ves_vendorspricecomparison/modalbox.css');
				$headBlock->addCss('ves_vendorspricecomparison/styles.css');
				$headBlock->addJs('ves_vendors/pricecomparison/modalbox.js');
				$headBlock->addJs('ves_vendors/pricecomparison/script.js');
			}
			
		}
		Mage::dispatchEvent('ves_vendor_pricecomparison_prepare_columns',array('block'=>$this));
		return $this;
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
	
	public function addColumn($name,$title,$block, $sortorder){
		$this->_columns[] = array(
			'name'	=> $name,
			'title'	=> $title,
			'block'	=> $block,
			'sort_order'	=> $sortorder,
		);
		if(sizeof($this->_columns) >1)
		usort($this->_columns, array($this,"_cmpOrder"));
	}
	
	public function getColumns(){
		return $this->_columns;
	}
	
	public function getColumnHtml($block, VES_Vendors_Model_Vendor $vendor, Mage_Catalog_Model_Product $product){
		if(is_string($block)){
			$block = $this->getLayout()->getBlock($block);
			$block->setVendor($vendor)->setProduct($product);
			return $block->toHtml();
		}elseif($block instanceof Mage_Core_Block_Abstract){
			$block->setVendor($vendor)->setProduct($product);
			return $block->toHtml();
		}
		return '';
	}
	
	public function canShowVendorProducts(){
		return !Mage::registry('vendor') && $this->getProduct()->getData('ves_enable_comparison');
	}
	/**
	 * Get Current Product
	 * @return Mage_Catalog_Model_Product
	 */
	public function getProduct(){
		return Mage::registry('product');
	}

    public function isShowSelectProduct(){
        $vendor = Mage::getSingleton('vendors/session')->getVendor()->getId();
        $products = $this->getVendorRelatedProducts();
        $vendor_ids = array();
        foreach($products as $product){
            if($product->getData("vendor_id") == $vendor) {return false;}
        }
        return true;
    }

    /**
     * Get price comparison products.
     */
	public function getVendorRelatedProducts(){
		if(!$this->getData('vendor_related_products')){
			$productCollection = Mage::getModel('catalog/product')->getCollection()
    			->addAttributeToFilter(
    			    array(
    			        array('attribute'=> 'vendor_parent_product','eq' => $this->getProduct()->getId()),
    			        array('attribute'=> 'entity_id','eq' => $this->getProduct()->getId()),
    			    )
    			)
				->addAttributeToFilter('vendor_id',array('gt' => 0))
				->setOrder('price','ASC');
			$this->prepareProductCollection($productCollection);
			$this->setData('vendor_related_products',$productCollection);
		}
		return $this->getData('vendor_related_products');
	}
	/**
     * Initialize product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addAttributeToFilter('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();
            
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }
    
    public function getImageUrl($file){
    	echo Mage::getBaseUrl('media').$file;
    }

    public function formatDescription($description){
        if(strlen($description) > $this->getDescriptionLength())
            return substr($description,0,strrpos(substr($description,0,$this->getDescriptionLength()),' ')).'...';

        return $description;
    }

    
    protected function _toHtml(){
    	return $this->canShowVendorProducts()?parent::_toHtml():'';
    }
    
}
