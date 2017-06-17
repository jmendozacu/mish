<?php

class VES_VendorsCategory_Model_Category extends Mage_Core_Model_Abstract
{
	/**
	 * Vendor who owns this category
	 * @var VES_Vendors_Model_Vendor
	 */
	protected $_vendor;
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscategory/category');
    }
	/**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
    	if(!$this->getUrlKey()){
    		$this->setUrlKey($this->formatUrlKey($this->getName()));
    	}
        return parent::_beforeSave();
    }
    
    public function _afterSave() {
    	/*Process URL rewrite*/
    	$baseUrlKey = Mage::getStoreConfig('vendors/vendor_page/url_key');
    	$suffix 	= Mage::getStoreConfig('catalog/seo/category_url_suffix');
    	$vendorId 	= $this->getVendorId();
		$vendor 	= Mage::getModel('vendors/vendor')->load($vendorId);;
		$vendorId 	= $vendor->getVendorId();
		$urlKey		= $this->getUrlKey();
		$idPath 	= $vendorId.'/category/'.$this->getId();
		
		$requestPath 	= $baseUrlKey.'/'.$vendorId.'/'.$urlKey.$suffix;
		$targetPath 	= $baseUrlKey.'/'.$vendorId.'/category/view/id/'.$this->getId();
		
		if(!$baseUrlKey){
			$requestPath 	= $vendorId.'/'.$this->getUrlKey().$suffix;
			$targetPath 	= $vendorId.'/category/view/id/'.$this->getId();
		}
		foreach(Mage::app()->getWebsite($vendor->getWebsiteId())->getStoreIds() as $storeId){
			$urlRewriteCollection = Mage::getModel('core/url_rewrite')->getCollection()
			->addFieldToFilter('id_path',$idPath)
			->addFieldToFilter('store_id',$storeId)
			;
			$urlRewriteId = '';
			if($urlRewriteCollection->count()) 
			{
				$urlRewriteId = $urlRewriteCollection->getFirstItem()->getId();
			}
			/*check request path is exist or not*/
			$existUrlRewriteObj	= Mage::getModel('core/url_rewrite')->getCollection()
				->addFieldToFilter('request_path', $requestPath)
				->addFieldToFilter('store_id', $storeId)
				;
				
			if($existUrlRewriteObj->count() && $existUrlRewriteObj->getFirstItem()->getId() != $urlRewriteId){
				$requestPath = $baseUrlKey.'/'.$vendorId.'/'.$urlKey.'-'.$this->getId().$suffix;
				if(!$baseUrlKey){
					$requestPath = $vendorId.'/'.$urlKey.'-'.$this->getId().$suffix;
				}
			}
			$urlRewrite = Mage::getModel('core/url_rewrite');
			$urlData = array(
				'is_system'		=> 1,
				'id_path'		=> $idPath,
				'request_path'	=> $requestPath,
				'target_path'	=> $targetPath,
				'is_vendors_url'=> 1,
				'store_id'		=> (int)$storeId,
			);
			$urlRewrite->setData($urlData);
			if($urlRewriteId) $urlRewrite->setId($urlRewriteId);
			$urlRewrite->save();
		}

    	return parent::_afterSave();
    }
    
    
    protected function _beforeDelete() {
    	/*
    	 * remove categories form product
    	*/
    	$productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('vendor_id',$this->getVendor()->getVendorId())
            ->addFieldToFilter('vendor_categories',array('finset'=>$this->getId()));
    	foreach($productCollection as $_product) {
    		$categoryIds = explode(',',$_product->getVendorCategories());
    		unset($categoryIds[array_search($this->getId(),$categoryIds)]);
    		
    		$_product->setData('vendor_categories',implode($categoryIds, ','));
    		$_product->save();
    	}
    	
    	
    	/*
    	 * delete children categories
    	 */
    	
    	$childrenCategoryIds = Mage::getModel('vendorscategory/category')->getAllChildrenCategoryIds($this);	//all children category ids
    	foreach($childrenCategoryIds as $_id) {
    		$category = Mage::getModel('vendorscategory/category')->load($_id);
    		$category->delete();
    	}
    	return parent::_beforeDelete();
    }
    
    /**
     * get all children category id(recursive mode)
     * @param unknown $category
     * @return Ambigous <multitype:, multitype:NULL >
     */
    public function getAllChildrenCategoryIds($category) {
    	$result = array();
    	$children_category = $category->getChildrenCategoryCollection();
    	foreach($children_category as $_cat) {
    		$result[] = $_cat->getId();
    		$result = array_merge($result, Mage::getModel('vendorscategory/category')->getAllChildrenCategoryIds($_cat));
    	}
    	return $result;
    }
    
    /**
     * get all parent category id(recursive mode)
     * @param unknown $category
     * @return Ambigous <multitype:, multitype:NULL >
     */
    public function getAllParentCategoryIds($category) {
    	$result = array();
    	if($category->getLevel() == '0') return $result;		//over
    	$parent_category = $category->getParentCategory();Mage::log($parent_category->getId());
    	$result[] = $parent_category->getId();
    	$result = array_merge($result, Mage::getModel('vendorscategory/category')->getAllParentCategoryIds($parent_category));
    	
    	return $result;
    }
    
    
	/**
     * Format URL key from name or defined key
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $str = Mage::helper('core')->removeAccents($str);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }
    
    
    /**
     * Get Parent category
     * @return VES_VendorsCatalog_Model_Category
     */
    public function getParentCategory(){
    	if($categoryId = $this->getData('parent_category_id')){
    		return Mage::getModel('vendorscategory/category')->load($categoryId);
    	}
    	return false;
    }
    
    public function getVendor(){
    	if(!$this->_vendor){
    		$this->_vendor = Mage::getModel('vendors/vendor')->load($this->getVendorId());
    	}
    	return $this->_vendor;
    }
    
    /**
     * get children category collection
     */
    public function getChildrenCategoryCollection($is_active = true){
    	$collection = Mage::getModel('vendorscategory/category')->getCollection()
    			->addFieldToFilter('vendor_id',$this->getVendorId())
    			->addFieldToFilter('parent_category_id',$this->getId())
    			->setOrder('sort_order','ASC');
    	if($is_active) 
    		return $collection->addFieldToFilter('is_active',1);
    	return $collection;
    }
    
    /**
     * get category url from vendorid param
     * provide for categories toolbar
     * @param unknown $vendorId
     * @return string
     */
    public function getCategoryUrl($vendorId) {
    	$urlRewrite = Mage::getModel('core/url_rewrite')->load($vendorId.'/category/'.$this->getId(),'id_path');
    	return Mage::getBaseUrl() . $urlRewrite->getData('request_path');
    }
    
    /**
     * get category page layout
     * @return Ambigous <mixed, NULL, unknown, multitype:, Varien_Object>
     */
    public function getCategoryLayout() {
    	return $this->getData('page_layout'); 
    }
    
    
    /**
     * get category url from current category
     * @return string
     */
    public function getCategoryLink() {
    	$vendorId = $this->getVendor()->getVendorId();
    	$urlRewrite = Mage::getModel('core/url_rewrite')->load($vendorId.'/category/'.$this->getId(),'id_path');
    	
    	return Mage::getBaseUrl() . $urlRewrite->getData('request_path');
    }

    public function getProductCount() {
    	$productCollection = Mage::getModel('catalog/product')->getCollection()
    	->addAttributeToSelect('*')->addFieldToFilter('vendor_categories',array('finset'=>$this->getId()));
    	
    	return (int)$productCollection->count();
    }

    /**
     * move category
     */
    public function move($parentId, $afterCategoryId) {
        if($parentId) {
            $parent = Mage::getModel('vendorscategory/category')->load($parentId);
        } else {
            $parent = Mage::helper('vendorscategory')->generateRootNode();
        }
        Mage::log($parent->getId());
        if ($parent->getId() == null or $parent->getId() < 0) {
            Mage::throwException(
                Mage::helper('catalog')->__('Category move operation is not possible: the new parent category was not found.')
            );
        }

        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('catalog')->__('Category move operation is not possible: the current category was not found.')
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('catalog')->__('Category move operation is not possible: parent category is equal to child category.')
            );
        }

        try {
            $this->_getResource()->changeParent($this, $parent, $afterCategoryId);
            $this->_getResource()->commit();
        }catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
    }
}