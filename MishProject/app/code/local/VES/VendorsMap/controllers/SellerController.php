<?php

class VES_VendorsMap_SellerController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	/*
	public function searchpAction(){
		$q =  $this->getRequest()->getParam("q");
		$vendor_id = array();
		$products = Mage::getModel("catalog/product")->getCollection()->addAttributeToFilter("vendor_id",array("neq"=>0))
		->addAttributeToFilter("name",array("like"=>"%".trim($q)."%"));
		$list = "<ul>";
		if(sizeof($products) != 0){
			foreach($products as $product){
				$vendor_id[] = $product->getVendorId();
			}
			$vendor_id = array_unique($vendor_id);
			$block = $this->getLayout()->createBlock('vendorsmap/search')->setTemplate('ves_vendorsmap/shop/search.phtml')->setVendorId($vendor_id);
			$list .=  $block->toHtml();
			
			$messages = $block->getMesssages();
			$neighborhoods = $block->getNeighborhoods();
			
			$list .= "</ul>";
			$result = array('load'=>true,'list'=>$list,'messages'=>$messages,'neighborhoods'=>$neighborhoods);
		}
		else{
			$result = array('load'=>false);
		}
		echo json_encode($result);
	}
	*/
	public function searchaAction(){
		$vendor_id = array();
        $name =  $this->getRequest()->getParam("name");
        if($name){
            $products = Mage::getModel("catalog/product")->getCollection()->addAttributeToFilter("vendor_id",array("neq"=>0))
                ->addAttributeToFilter("name",array("like"=>"%".trim($name)."%"));
        }
        else{
            $result = array('load'=>false);
            echo json_encode($result);
            exit;
        }

        $data = array(
            "city"=>$this->getRequest()->getParam("city"),
            "country"=>$this->getRequest()->getParam("country"),
            "region"=>$this->getRequest()->getParam("region"),
            "region_id"=>$this->getRequest()->getParam("region_id"),
            "zip"=>$this->getRequest()->getParam("zip"),
            "attribute"=>$this->getRequest()->getParam("attribute"),
        );

		$list = "<ul>";
		if(sizeof($products) != 0){
			foreach($products as $product){
				$vendor_id[] = $product->getVendorId();
			}
			$vendor_id = array_unique($vendor_id);
			$block = $this->getLayout()->createBlock('vendorsmap/search')->setTemplate('ves_vendorsmap/shop/search.phtml')->setVendorId($vendor_id)->setParamData($data);
			$list .=  $block->toHtml();
			$messages = $block->getMesssages();
			$neighborhoods = $block->getNeighborhoods();
			$list .= "</ul>";
			$result = array('load'=>true,'list'=>$list,'messages'=>$messages,'neighborhoods'=>$neighborhoods);
		}
		else{
			$result = array('load'=>false);
		}
		echo json_encode($result);
	}
}