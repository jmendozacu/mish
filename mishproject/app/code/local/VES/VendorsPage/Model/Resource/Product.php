<?php

class VES_VendorsPage_Model_Resource_Product extends Mage_Catalog_Model_Resource_Product
{   
    /**
     * Process URL Rewrite
     */
    public function processUrlRewrite(){
    	$tableName = $this->getTable('core/url_rewrite');
    	$newUrlKey = Mage::getStoreConfig('vendors/vendor_page/url_key');
   	
    	$productCollection = Mage::getModel('catalog/product')->getCollection()
    		->addAttributeToSelect('url_key')
    		->addAttributeToSelect('name')
    		;
    	$vendors = array();
    	foreach($productCollection as $product){
	    	$vendorId 	= $product->getVendorId();
	    	if(!$vendorId) continue;
	    	
	    	if(!isset($vendors[$vendorId])){
	    		$vendors[$vendorId] = Mage::getModel('vendors/vendor')->load($vendorId);
	    	}
	    	$vendor 	= $vendors[$vendorId];
	    	$vendorId 	= $vendor->getVendorId();
	    	
	    	$idPath 	= $vendorId.'/product/'.$product->getId();
	    	$suffix 	= Mage::getStoreConfig('catalog/seo/product_url_suffix');
	    	$urlKey 	= $product->getUrlKey()?$product->getUrlKey():Mage::helper('vendorsproduct')->formatUrlKey($product->getName());
	    	
	    	
    		$baseUrlKey = Mage::getStoreConfig('vendors/vendor_page/url_key');
			$requestPath= $baseUrlKey.'/'.$vendorId.'/'.$urlKey.$suffix;
			$targetPath = $baseUrlKey.'/'.$vendorId.'/product/view/id/'.$product->getId();
				
			if(!$baseUrlKey){
				$requestPath	= $vendorId.'/'.$urlKey.$suffix;
				$targetPath 	= $vendorId.'/product/view/id/'.$product->getId();
			}
				
	    	/**
	    	 * if isset idpath
	    	 */
			foreach($product->getStoreIds() as $storeId){
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
					$requestPath = $baseUrlKey.'/'.$vendorId.'/'.$urlKey.'-'.$product->getId().$suffix;
					if(!$baseUrlKey){
						$requestPath = $vendorId.'/'.$urlKey.'-'.$product->getId().$suffix;
					}
				}
				
				
				$urlRewrite = Mage::getModel('core/url_rewrite');
				$urlData = array(
					'is_system'		=> 1,
					'id_path'		=> $idPath,
					'request_path'	=> $requestPath,
					'target_path'	=> $targetPath,
					'is_vendors_url'=> 1,
					'product_id'	=> $product->getId(),
					'store_id'		=> $storeId,
				);
				$urlRewrite->setData($urlData);
				if($urlRewriteId) $urlRewrite->setId($urlRewriteId);
				$urlRewrite->save();
			}
    	}
    }
}