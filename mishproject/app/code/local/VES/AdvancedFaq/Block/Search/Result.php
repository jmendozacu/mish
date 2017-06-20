<?php
class OTTO_AdvancedFaq_Block_Search_Result extends OTTO_AdvancedFaq_Block_Abstract
{
	public function __construct(){
		$this->setTemplate("otto_advancedfaq/search/theme1/result.phtml");
		return parent::__construct();
	}
	public function _prepareLayout()
	{
		parent::_prepareLayout();
	
	
		if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {

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
			))->addCrumb('kbasehome',$link)
			->addCrumb('search', array(
					'label'	=> Mage::helper('advancedfaq')->__('Search results'),
					'title'	=> Mage::helper('advancedfaq')->__('Search results'),
			));
		}
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			if(!$title) $title = "Search Results".' - '.$this->getHomeTitle();
			$headBlock->setTitle($title);
			if($keywords = Mage::helper("advancedfaq")->getMetaKeyword()){
				$headBlock->setKeywords($keywords);
			}
			if($metaDescription = Mage::helper("advancedfaq")->getMetaDescription()){
				$headBlock->setDescription($metaDescription);
			}
		}
		return $this;
	}
	
	/**
	 *
	 * get Description Chart
	 */
	public function getDescriptionChart(){
		return Mage::helper('advancedfaq')->getDescriptionLength() ;
	}
	
    public function getKeyQuery(){
    	$key = "";
    	if(Mage::registry("key_query")) $key = Mage::registry("key_query");
 
    	return $key;
    }
    
  
    
    public function getFaqs(){
    	$key = "";
    	if(Mage::registry("key_query")) $key = Mage::registry("key_query");
    	//$faq = Mage::getModel('kbase/faq')->getCollection()->addFieldToFilter("question",array('like' =>'%'.$key.'%'));
    	$faqs = Mage::getModel('advancedfaq/faq')->getCollection()->addFieldToFilter("show_on",array('eq' =>OTTO_AdvancedFaq_Model_Status::STATUS_ENABLED));
    	
    	if(Mage::registry("current_seller")){
    		$seller =  Mage::registry("current_seller");
    		$faqs->addFieldToFilter('seller_id',$seller->getId());
    	}
    	//var_dump($faqs);exit;
    	$data = array();
    	$faq_after = array();
    	foreach ($faqs as $faq){
    		$cate = Mage::getModel("advancedfaq/category")->load($faq->getData('category_id'));
    		if($cate->getData('status') != OTTO_AdvancedFaq_Model_Status::STATUS_ENABLED) continue;
    		if(preg_match("/".$key."/is",$faq->getData('question'),$result)){
    			$data[] = $faq;
    		}
    		else{
    			$faq_after[] = $faq;
    		}
    	}
    	foreach ($faq_after as $faq){
    		if(preg_match("/".$key."/is",$faq->getData('answer'),$result)){
    			$data[] = $faq;
    		}
    	}
    	return $data;
    }
}