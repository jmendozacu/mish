<?php
class OTTO_AdvancedFaq_Block_Faq extends OTTO_AdvancedFaq_Block_Abstract
{
	public function _prepareLayout()
    {
    	if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
    		$category = $this->getCategory();
    		
    		$link = array(
                'label'	=> $this->getHomeTitle(),
                'title'	=> $this->getHomeTitle(),
                'link'	=> Mage::helper('advancedfaq')->getUrlBase().$this->getUrlKey(),
            	//'link'	=> Mage::helper('advancedfaq')->getUrlBase()."faq/home",
            );
    		
    		
    		if(Mage::registry("current_seller")){
    			$seller = Mage::registry("current_seller");
    			$url = Mage::helper("sellerspage")->getUrl($seller);
    			$link['link'] = Mage::helper("sellerspage")->getUrl($seller,$this->getUrlKey());
    		}
    		else{
    			$url = Mage::getUrl();
    		}
    		
            $breadcrumbsBlock->addCrumb('home', array(
                'label'	=> Mage::helper('advancedfaq')->__('Home'),
                'title'	=> Mage::helper('advancedfaq')->__('Home'),
                'link'	=> $url,
            ))
            ->addCrumb('kbasehome', $link)
            ->addCrumb('new_faq', array(
            		'label'	=> Mage::helper('advancedfaq')->__('New FAQ'),
            		'title'	=> Mage::helper('advancedfaq')->__('New FAQ'),
            ));
        }
    	if ($headBlock = $this->getLayout()->getBlock('head')) {
			
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
     * Get current Article
     * @return OTTO_Kbase_Model_Faq
     */
 	public function getFaq(){ 
        return Mage::registry('kbase_faq');
    }
    /**
     * Get Current Category
     * @return OTTO_AdvancedFaq_Model_Category
     */
    public function getCategory(){
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
    	//return Mage::registry('kbase_category');
    }
    /** check enable rating
     * 
     */
    public function isEnableRating(){
    	if(Mage::helper('advancedfaq')->getArticlesRating() == 1) return true;
    	return false;
    }
	/**
	 * get Url rating
	 */
    public function getUrlRating($id){
    	/*
    	if(Mage::helper('advancedfaq')->getSecureUrl() == 1){
    		$url = $this->getUrl("sellerspage/article/vote",array('id'=>$id,'_secure'=>true));
    	}
    	else{
    		$url = $this->getUrl("sellerspage/article/vote",array('id'=>$id));
    	}
    	*/
    	$seller =  Mage::registry("current_seller");
    	$url = Mage::helper("sellerspage")->getUrl($seller,"article/vote/id/".$id);
    	return $url;
    }
    /**
     * get Url New
     */
    public function getUrlNew($id){
    	/*
    	if(Mage::helper('advancedfaq')->getSecureUrl() == 1){
    		$url = $this->getUrl("faq/new/save",array('id'=>$id,'_secure'=>true));
    		if(Mage::registry("current_seller")){
    			$seller =  Mage::registry("current_seller");
    			$url = $this->getUrl("faq/new/save",array('id'=>$id,"seller_id"=>$seller->getId(),'_secure'=>true));
    		}
    	}
    	else{

    		$url = $this->getUrl("faq/new/save",array('id'=>$id));
    		if(Mage::registry("current_seller")){
    			$seller =  Mage::registry("current_seller");
    			$url = $this->getUrl("faq/new/save",array('id'=>$id,"seller_id"=>$seller->getId()));
    		}
    	}
    	*/
    	$seller =  Mage::registry("current_seller");
    	$url = Mage::helper("sellerspage")->getUrl($seller,"new/save");
    	return $url;
    }

}
