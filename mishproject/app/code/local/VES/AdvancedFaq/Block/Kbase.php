<?php
class OTTO_AdvancedFaq_Block_Kbase extends OTTO_AdvancedFaq_Block_Abstract
{
	public function _prepareLayout()
    {
    	if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
    		if(Mage::registry("current_seller")){
    			$seller = Mage::registry("current_seller");
    			$url = Mage::helper("sellerspage")->getUrl($seller);
    			//$url= $this->getUrl($seller->getData("seller_id")."/".$this->getUrlKey()."/new");
    		}
    		else{
    			$url = Mage::getUrl();
    		}
            $breadcrumbsBlock->addCrumb('home', array(
                'label'	=> Mage::helper('advancedfaq')->__('Home'),
                'title'	=> Mage::helper('advancedfaq')->__('Home'),
                'link'	=> $url,
            ))->addCrumb('kbasehome', array(
                'label'	=> $this->getHomeTitle(),
                'title'	=> $this->getHomeTitle(),
            ));
    	}
    	
    	if ($headBlock = $this->getLayout()->getBlock('head')) {
			$title = $this->getHomeTitle();
			if($storeName = Mage::getStoreConfig('general/store_information/name')) $title .=' - '.$storeName;
			$headBlock->setTitle($title);
			if($keywords = Mage::helper("advancedfaq")->getMetaKeyword()){
				$headBlock->setKeywords($keywords);
			}
			if($metaDescription = Mage::helper("advancedfaq")->getMetaDescription()){
				$headBlock->setDescription($metaDescription);
			}
		}
		
	
		return parent::_prepareLayout();
    }

    /**
     * Get all categories
     * @return OTTO_AdvancedFaq_Model_Mysql4_Category_Collection
     */
    public function getAllCategories(){
    	$storeId = Mage::app()->getStore()->getId();
    	$data = array();
    	$con = array(
    		array('finset'=>$storeId),
    		array('finset'=>0),
    	);
    	$category = Mage::getModel("advancedfaq/category")->getCollection()->addFieldToFilter('status',array("eq"=>OTTO_AdvancedFaq_Model_Status::STATUS_ENABLED))->addFieldToFilter('store_id',$con);

    	if(Mage::registry("current_seller")){
    		$seller =  Mage::registry("current_seller");
    		$category->addFieldToFilter('seller_id',$seller->getId());
    	}
    	return $category;
    }

   
    /**
     * Get all faqs related to category
     * @param int $category_id
     * @return OTTO_AdvancedFaq_Model_Mysql4_Faq_Collection
     */
    public function canShowRatingForm($id){
    	$cookie = Mage::getSingleton('core/cookie');
    	$ratingIds = explode(',',$cookie->get('otto_advancedfaq_rating_ids'));
    	return (!in_array($id, $ratingIds));
    }
    
    public function getFaq($category_id){

    	$faq = Mage::getModel("advancedfaq/faq")->getCollection()->addFieldToFilter('category_id',array('finset'=>$category_id))->addFieldToFilter('show_on',array("eq"=>OTTO_AdvancedFaq_Model_Status::STATUS_ENABLED))->setOrder('sort_order','ASC');
    	

    	if(Mage::registry("current_seller")){
    		$seller =  Mage::registry("current_seller");
    		$faq->addFieldToFilter('seller_id',$seller->getId());
    	}
    	
    	return $faq;
    }

 
    /**
     * sort block
     */
    public function sortBlockHtml(){
    	return $this->getChildHtml("block_faq");
    }

    /**
     * get Url new
     */
    public function getUrlNew(){
    	//return "test";
    	$url= $this->getUrl($this->getUrlKey()."/new");
    	if(Mage::registry("current_seller")){
    		$seller = Mage::registry("current_seller");
    		$url = Mage::helper("sellerspage")->getUrl($seller,$this->getUrlKey()."/new");
    		//$url= $this->getUrl($seller->getData("seller_id")."/".$this->getUrlKey()."/new");
    	}
    	return $url;
    }
}