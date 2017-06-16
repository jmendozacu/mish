<?php

class OTTO_AdvancedFaq_Model_Faq extends Mage_Core_Model_Abstract
{
 	public function _construct()
    {
        parent::_construct();
        $this->_init('advancedfaq/faq');
    }
    public function getCategoryOptions(){
    	$data = array();
    	$category = Mage::getModel('advancedfaq/category')->getCollection();
    	foreach ($category as $cat){
    		$data[] = array('value'=>$cat->getData('category_id'), 'label'=>$cat->getData('title'));
    	}
    	return $data;
    }
    public function getCategoryOptionsSellers(){
    	$data = array();
    	$sellerId = Mage::getSingleton('sellers/session')->getSellerId();
    	$category = Mage::getModel('advancedfaq/category')->getCollection();
    	$category->addFieldToFilter('seller_id',$sellerId);
    	foreach ($category as $cat){
    		$data[] = array('value'=>$cat->getData('category_id'), 'label'=>$cat->getData('title'));
    	}
    	return $data;
    }
    public function checkIdentifier($id,$storeId){
    	
    	$con = array(
    		array('finset'=>$storeId),
    		array('finset'=>0),
    	);
    	$faq = Mage::getModel('advancedfaq/faq')->getCollection()
    		->addFieldToFilter('url_key',array("eq"=>$id))
    		->addFieldToFilter('status',array("eq"=>OTTO_Kbase_Model_Status::STATUS_ENABLED))
    		->addFieldToFilter('store_id',$con)
    		->getFirstItem();
    	return $faq;
    }
    public function saveTags($tags){
    	$tags = explode(',', $tags);
    	if(is_array($tags) && sizeof($tags)!=0){
    		foreach($tags as $tag){
    			$check_tag = Mage::getModel('advancedfaq/tag')->load($tag,'name');
    			if(!$check_tag->getId()){
    				Mage::getModel('advancedfaq/tag')->setData('name',$tag)->setData('status',OTTO_Kbase_Model_Status::STATUS_ENABLED)->setId()->save();
    			}
    		}
    		return;
    	}
		return ;
    }
}
