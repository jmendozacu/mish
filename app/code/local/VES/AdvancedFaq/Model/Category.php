<?php

class OTTO_AdvancedFaq_Model_Category extends Mage_Core_Model_Abstract
{
 	public function _construct()
    {
        parent::_construct();
        $this->_init('advancedfaq/category');
    }
    public function checkIdentifier($id,$storeId){
    	$con = array(
    		array('finset'=>$storeId),
    		array('finset'=>0),
    	);
    	$category = Mage::getModel('advancedfaq/category')->getCollection()
    		->addFieldToFilter('url_key',array("eq"=>$id))
    		->addFieldToFilter('status',array("eq"=>OTTO_AdvancedFaq_Model_Status::STATUS_ENABLED))
    		->addFieldToFilter('store_id',$con)
    		->getFirstItem();
    	return $category;
    }
    public function checkIdentifierSeller($id,$storeId,$seller_id){
    	$con = array(
    			array('finset'=>$storeId),
    			array('finset'=>0),
    	);
    	$category = Mage::getModel('advancedfaq/category')->getCollection()
    	->addFieldToFilter('url_key',array("eq"=>$id))
    	->addFieldToFilter('status',array("eq"=>OTTO_AdvancedFaq_Model_Status::STATUS_ENABLED))
    	->addFieldToFilter('store_id',$con)
    	->addFieldToFilter('seller_id',$seller_id)
    	->getFirstItem();
    	return $category;
    }
    public function getCategoryOpTion(){
    	$data = array();
    	$categorys = Mage::getModel('advancedfaq/category')->getCollection();
		foreach($categorys as $cate){
			$data[$cate->getId()]= $cate->getData('title');
		}
    	return $data;
    }
}