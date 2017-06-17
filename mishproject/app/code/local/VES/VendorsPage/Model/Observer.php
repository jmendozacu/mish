<?php

class VES_VendorsPage_Model_Observer
{
	public function handleControllerFrontInitRouters($observer) 
    {
    	$observer->getEvent()->getFront()
            ->addRouter('vendorspage', new VES_VendorsPage_Controller_Router());
    }
    
	/**
	 * Add layout handle for all vendor pages
	 * @param unknown_type $observer
	 */
	public function controller_action_layout_load_before(Varien_Event_Observer $observer){
		if(Mage::registry('vendor')){
			$layout = $observer->getEvent()->getLayout();
			$layout->getUpdate()->addHandle(VES_VendorsPage_Helper_Data::VENDOR_LAYOUT_HANDLE);
		}
	}
	
	/**
     * Process URL Rewrite.
     * @param Varien_Event_Observer $observer
     */
    public function catalog_product_save_after(Varien_Event_Observer  $observer) {
    	/*Process URL rewrite*/
    	$product 	= $observer->getProduct();
    	$vendorId 	= $product->getVendorId();
    	$vendor 	= Mage::getModel('vendors/vendor')->load($vendorId);
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