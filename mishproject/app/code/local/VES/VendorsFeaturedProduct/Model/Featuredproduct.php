<?php

class VES_VendorsFeaturedProduct_Model_Featuredproduct extends Mage_Core_Model_Abstract
{
	public function getFeaturedProducts($vendor_id,$size=false,$cat_id= false, $order_by = false, $direction="desc"){
		$storeId = Mage::app()->getStore()->getId();
		$products = Mage::getResourceModel('reports/product_collection')
						//->joinField('category_id','vendorscategory/category','category_id','product_id=entity_id',null,'left')
						->setStoreId($storeId)
						->addStoreFilter($storeId)	
						->addAttributeToSelect('*')
					    ->addAttributeToFilter('visibility', array(
	                    	Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
	                    	Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                  		))
                  		->addAttributeToFilter('status',1)
						->addAttributeToFilter('ves_vendor_featured', true)
						->addAttributeToFilter('vendor_id', $vendor_id);
					    
		if($size) $products->setPageSize($size);
		if($cat_id) $products->addAttributeToFilter('vendor_categories', array('finset' => $cat_id));
		if($order_by) $products->setOrder($order_by,$direction);;
		return $products;
	}
}