<?php
class OTTO_AdvancedFaq_Block_Category extends OTTO_AdvancedFaq_Block_Abstract
{
	public function __construct(){
		$this->setTemplate("otto_advancedfaq/category/theme1/category.phtml");
		return parent::__construct();
	}
	
	public function _prepareLayout()
	{
		parent::_prepareLayout();

		$pager = $this->getLayout()->createBlock('advancedfaq/page_html_pager', 'kbase.pager')
			->setCollection($this->getFaqs());
		//$pager->setAvailableLimit($limit);
		$this->setChild('pager', $pager);
		
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
				//$url= $this->getUrl($seller->getData("seller_id")."/".$this->getUrlKey()."/new");
			}
			else{
				$url = Mage::getUrl();
			}
			

			
            $breadcrumbsBlock->addCrumb('home', array(
                'label'	=> Mage::helper('advancedfaq')->__('Home'),
                'title'	=> Mage::helper('advancedfaq')->__('Home'),
                'link'	=> $url,
            ))->addCrumb('kbasehome', $link)
            ->addCrumb('category_'.$this->getCategory()->getId(), array(
                'label'	=> $this->getCategory()->getTitle(),
                'title'	=> $this->getCategory()->getTitle(),
            ));
        }
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			
			$title = $this->getCategory()->getMetaTitle();
			if(!$title) $title = $this->getCategory()->getTitle().' - '.$this->getHomeTitle();
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
	
	public function getPagerHtml(){
		return $this->getChildHtml('pager');
	}
	
	/**
	 * Get current category
	 * @return OTTO_AdvancedFaq_Model_Category
	 */
    public function getCategory(){ 
        return Mage::registry('kbase_category');
        
    }
    
    /**
     * Get all faqs related to current category
     * @return OTTO_AdvancedFaq_Model_Mysql4_Faq_Collection
     */
    public function getFaqs(){
	   	if (!$this->hasData('faqs')) {
        	$category = $this->getCategory();
        	$storeId = Mage::app()->getStore()->getId();
	    	$storeCond = array(
	    		array('finset'=>$storeId),
	    		array('finset'=>0),
	    	);
            $faqs = Mage::getModel("advancedfaq/faq")->getCollection()
            	->addFieldToFilter('category_id',array('eq'=>$category->getData('category_id')))
            	->addFieldToFilter('show_on',array("eq"=>OTTO_AdvancedFaq_Model_Status::STATUS_ENABLED))
            	->setOrder('created_time','DESC');;
            
	   	if(Mage::registry("current_seller")){
    		$seller =  Mage::registry("current_seller");
    		$faqs->addFieldToFilter('seller_id',$seller->getId());
    	}
            	
            $this->setData('faqs',$faqs);
        }
        return $this->getData('faqs');
	}
	/**
	 * 
	 * get Description Chart
	 */
	public function getDescriptionChart(){
		return Mage::helper('advancedfaq')->getDescriptionLength() ;
	}
}