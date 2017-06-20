<?php
class VES_VendorsPriceComparison_ProcessController extends Mage_Core_Controller_Front_Action
{

	public function selectProductAction(){
		$parentProductId = $this->getRequest()->getParam('parent_product');
		Mage::registry('parent_product_id',$parentProductId);
		$this->loadLayout();
		$this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('pricecomparison/vendor_product_edit'));
		$this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('pricecomparison/vendor_product_edit_tabs'));
		$this->renderLayout();
	}

    public function deleteitemAction(){
        try {
        	if(!Mage::getSingleton('vendors/session')->isLoggedIn()) throw new Exception(Mage::helper('pricecomparison')->__('Please login to your vendor account to do this action.'));
        	$currentProductId 	= $this->getRequest()->getParam("current_product_id");
        	$product_id 		= $this->getRequest()->getParam("product_id");
	        $product 		= Mage::getModel("catalog/product")->load($product_id);
	        $currentProduct = Mage::getModel('catalog/product')->load($currentProductId);
	        if(!$product_id || !$currentProductId || !$product->getId() || !$currentProduct->getId()) throw new Exception(Mage::helper('pricecomparison')->__('You can not run this action'));
	        Mage::register("product",$currentProduct);
	        Mage::register("current_product",$currentProduct);
	        /*IF the removed product is the parent product.*/
        	if($product->getData('ves_enable_comparison')){
        	    $childProductCollection = Mage::getModel('catalog/product')->getCollection()
            	    ->addAttributeToFilter('vendor_parent_product',array('eq'=>$product->getId()));
        	    
        	    if($childProductCollection->count()){
        	        /*Assign new parent products to the first child product*/
        	        $newParentProduct = $childProductCollection->getFirstItem();
        	        $newParentProduct->setData("vendor_parent_product",0)->getResource()->saveAttribute($newParentProduct, 'vendor_parent_product');
        	        $newParentProduct->setData("ves_enable_comparison",1)->getResource()->saveAttribute($newParentProduct, 'ves_enable_comparison');
        	        
        	        /*Assign new parent product for all child product.*/
        	        foreach($childProductCollection as $childProduct){
        	            if($childProduct->getId() == $newParentProduct->getId()) continue;
        	            $childProduct->setData("vendor_parent_product",$newParentProduct->getId())->getResource()->saveAttribute($childProduct, 'vendor_parent_product');
        	        }
        	        
        	        $product->setData("ves_enable_comparison",0)->getResource()->saveAttribute($product, 'ves_enable_comparison');
        	    }
        	}else{
        	    $product->setData("vendor_parent_product",0)->getResource()->saveAttribute($product, 'vendor_parent_product');
        	}
            $this->loadLayout(false);
            $this->renderLayout();
        }catch (Exception $e) {
            $result = array('success'=>false,"msg"=>$e->getMessage());
            echo json_encode($result);
        }
    }

    public function showListAction(){
        $page =  $this->getRequest()->getParam("page");
        $block = $this->getLayout()->createBlock('pricecomparison/product_list')->setTemplate('ves_pricecomparison/vendor_list/product_ajax.phtml')->setPageNumber($page);
        $result = array('success'=>true,"list_item"=>$block->toHtml(),"page_html"=>$block->getPageHtml());
        echo json_encode($result);
    }

    public function searchAction(){
        $page =  $this->getRequest()->getParam("page");
        $q =  $this->getRequest()->getParam("q");
        $block = $this->getLayout()->createBlock('pricecomparison/product_search')->setTemplate('ves_pricecomparison/vendor_list/product_ajax.phtml')->setPageNumber($page)->setQ($q);
        $result = array('success'=>true,"list_item"=>$block->toHtml(),"page_html"=>$block->getPageHtml());
        echo json_encode($result);
    }

    public function chooseitemAction(){
        $this->loadLayout(false);
        $id =  $this->getRequest()->getParam("id");
        $currentProductId =  $this->getRequest()->getParam("current_id");
    	$relatedProduct = Mage::getModel('catalog/product')->load($currentProductId);

        $product = Mage::getModel("catalog/product")->load($id);

        Mage::register("product",$relatedProduct);
        Mage::register("current_product",$relatedProduct);
        
        try {
            $product->setData('vendor_parent_product',$relatedProduct->getId())->getResource()->saveAttribute($product, 'vendor_parent_product');
            $this->renderLayout();
        }catch (Exception $e) {
            $result = array('success'=>false,"msg"=>$e->getMessage());
            echo json_encode($result);
        }
    }
}