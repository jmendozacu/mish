<?php
class VES_VendorsPriceComparison_Vendor_Comparison_ProcessController extends Mage_Core_Controller_Front_Action
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
    	$currentProductId 	= $this->getRequest()->getParam("current_product_id");
        $product_id 		= $this->getRequest()->getParam("product_id");
		$this->loadLayout();
		$this->renderLayout();
		return;
        
        try {
	        $product 		= Mage::getModel("catalog/product")->load($product_id);
	        $currentProduct = Mage::getModel('catalog/product')->load($currentProductId);
	        if(!$product_id || !$currentProductId || !$product->getId() || $currentProduct->getId()) throw new Exception(Mage::helper('pricecomparison')->__('You can not run this action'));
	        Mage::register("product",$currentProduct);
	        Mage::register("current_product",$currentProduct);
        	
            $product->setData("vendor_relation_key",'')->save();
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
		$key = $relatedProduct->getData('vendor_relation_key');
		if(!$key){
			$key = md5(Mage::getModel('core/date')->timestamp());
			$relatedProduct->setData('vendor_relation_key',$key)->getResource()->saveAttribute($relatedProduct, 'vendor_relation_key');
		}
        $product = Mage::getModel("catalog/product")->load($id);

        Mage::register("product",Mage::getModel('catalog/product')->load($currentProductId));
        Mage::register("current_product",Mage::getModel('catalog/product')->load($currentProductId));
        try {
            $product->setData('vendor_relation_key',$key)->save();
            $this->renderLayout();
        }catch (Exception $e) {
            $result = array('success'=>false,"msg"=>$e->getMessage());
            echo json_encode($result);
        }
    }
}