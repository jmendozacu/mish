<?php
class VES_BannerManager_Block_Banner extends Mage_Core_Block_Template
{
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_bannermanager_app_type_banner',array('result'=>$result));
		return $result->getIsEnabled();
	}
	public function _construct()
    {
    	
    }
	public function _toHtml(){
		if(!Mage::getStoreConfig('bannermanager/config/enabled') || !$this->isEnabled()) return;
		return parent::_toHtml();
	}
    public function _beforeToHtml()
    {
		
    	if(!Mage::getStoreConfig('bannermanager/config/enabled') || !$this->isEnabled()) return;

    	if($this->getBanner()){
			
			$this->setTemplate('ves_bannermanager/slider.phtml');
			
    		$cache_lifetime = Mage::getStoreConfig('bannermanager/config/cache_time');
	    	$cache_lifetime = ($cache_lifetime && is_numeric($cache_lifetime))?$cache_lifetime:24*60*60; 
	        $this->addData(array(
	            'cache_lifetime'    => $cache_lifetime,/* (s) */
	            'cache_tags'        => array(VES_BannerManager_Model_Banner::CACHE_TAG."_".$this->getBanner()->getId()),
	        	'cache_key'         => $this->getBanner()->getId(),
	        ));
		}
    	return parent::_beforeToHtml();
    }
	public function _prepareLayout()
    {
    	return parent::_prepareLayout();
    }
    public function getItems()
    {
    	$banner_id = $this->getBannerId()?$this->getBannerId():false;
    	return $this->getBanner()?$this->getBanner()->getAllItems():false;
    }
    public function getImageUrl($file_name)
    {
    	return Mage::getBaseUrl('media').$file_name;
    }
    public function getBanner()
    {
    	$banner_id = $this->getBannerId()?$this->getBannerId():false;
    	if(!$banner_id) return false;
    	$banner = Mage::getModel('bannermanager/banner')->loadByIdentifier($banner_id);
		
		$nivoData = unserialize($banner->getNivoOptions());
        $nivoData['nivoeffect'] = explode(',', $nivoData['effect']);

        unset($nivoData['effect']);
        $banner->addData($nivoData);
		
    	if(!$banner) return false;
    	return($banner->getStatus())?$banner:false;
    }
    
	
    public function filterDescription($description)

    {

        $processor = Mage::helper('cms')->getPageTemplateProcessor();

        return $processor->filter($description);

    }



    public function getDescriptionClassName($position)

    {

        switch ($position) {

            case 1:

                return "easyslide-description-top";

                break;

            case 2:

                return "easyslide-description-right";

                break;

            case 3:

                return "easyslide-description-bottom";

                break;

            case 4:

                return "easyslide-description-left";

                break;

        }

    }



    public function getBackgroundClassName($background)

    {

        switch ($background) {

            case 1:

                return "easyslide-background-light";

                break;

            case 2:

                return "easyslide-background-dark";

                break;

        }

    }



    public function getSlideClassName($slide)

    {

        switch ($slide['target_mode']) {

            case 1:

                return 'target-blank';

            case 2:

                return 'target-popup';

            case 0:

            default:

                return 'target-self';

        }

    }
	/*
    public function getItemUrl($bannerItem)
    {
    	$helper = Mage::helper('cms');
    	
        $processor = $helper->getBlockTemplateProcessor();
        return $processor->filter($bannerItem->getUrl());        
    }
	*/
}