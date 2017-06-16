<?php



class VES_VendorsPriceComparison_Model_Observer

{

	/**

	 * Hide products which relate to an other product

	 * @param Varien_Event_Observer $observer

	 */

    public function ves_vendorsproduct_product_list_prepare_after(Varien_Event_Observer $observer){

		$collection = $observer->getData('collection');

		//$collection->addAttributeToFilter('vendor_parent_product',0);



    }

    

    /**

     * If the product is enabled for price comparison set has options to true

     * @param Varien_Event_Observer $observer

     */

	public function catalog_product_load_after(Varien_Event_Observer $observer){

		$product = $observer->getProduct();

		if($product->getData('ves_enable_comparison')){

			$product->setHasOptions(true);

		}

	}

	

	public function catalog_product_save_before(Varien_Event_Observer $observer){

		$product = $observer->getProduct();

		if($relatedProductId = Mage::app()->getFrontController()->getRequest()->getParam('pricecomparison')){

			if($product->getId() == $relatedProductId) return;

			

			$relatedProduct = Mage::getModel('catalog/product')->load($relatedProductId);

			if(!$relatedProduct->getData('ves_enable_comparison')){

			    $relatedProduct->setData('ves_enable_comparison',1)->getResource()->saveAttribute($relatedProduct, 'ves_enable_comparison');

			}

			if($relatedProduct->getId()) $product->setData('vendor_parent_product',$relatedProductId);

		}

	}

	

	

	public function catalog_product_delete_before(Varien_Event_Observer $observer){

		$product = $observer->getProduct();

		$product->load($product->getId());

		if($product->getData('ves_enable_comparison')){

			$productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_parent_product',$product->getId());

			if($productCollection->count()){

				$newParentProduct = $productCollection->getFirstItem();

				$newParentProduct->setData("vendor_parent_product",0)->getResource()->saveAttribute($newParentProduct, 'vendor_parent_product');

    	        $newParentProduct->setData("ves_enable_comparison",1)->getResource()->saveAttribute($newParentProduct, 'ves_enable_comparison');

    	        

    	        /*Assign new parent product for all child product.*/

    	        foreach($productCollection as $childProduct){

    	            if($childProduct->getId() == $newParentProduct->getId()) continue;

    	            $childProduct->setData("vendor_parent_product",$newParentProduct->getId())->getResource()->saveAttribute($childProduct, 'vendor_parent_product');

    	        }

			}			

		}

	}



    /**

     * join select with catalog_product_entity_int table

     * to get vendor_child_product attribute.

     * not use addAttributeToFilter in Collection.

     * @param $observer

     */

    public function catalog_prepare_price_select($observer) {

/*         $attribute_code = 'vendor_child_product';

        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attribute_code);

        $select = $observer->getSelect();

        $resource = Mage::getSingleton('core/resource');

        Mage::log('ob'.$select->__toString());

        $select->joinInner(array('at_child_product' => $resource->getTableName('catalog_product_entity_int')),

                            'at_child_product.entity_id = e.entity_id and at_child_product.attribute_id = '.$attribute->getAttributeId().' and at_child_product.store_id = 0')



                ->where('at_child_product.value = ?', '0')

        ;



        Mage::log('ob after '.$select->__toString()); */





    }

}