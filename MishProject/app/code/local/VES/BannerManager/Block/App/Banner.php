<?php
class VES_BannerManager_Block_App_Banner extends VES_BannerManager_Block_Banner{
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_bannermanager_app_type_banner',array('result'=>$result));
		return $result->getIsEnabled();
	}
	public function _toHtml(){
		if(!Mage::getStoreConfig('bannermanager/config/enabled') || !$this->isEnabled()){
			return;
		} 
		return parent::_toHtml();
	}
	
	public function setApp($app){
		$this->setData('app',$app);
		$options = $app->getOptionsByCode('banner_option');
		if(!sizeof($options)) return;
		$optionValue = json_decode($options[0]->getValue(),true);
		/*Get navigation template*/
		$bannerId	= $optionValue['banner_id'];
		$banner		= Mage::getModel('bannermanager/banner')->load($bannerId);
		
		$nivoData = unserialize($banner->getNivoOptions());
        $nivoData['nivoeffect'] = explode(',', $nivoData['effect']);

        unset($nivoData['effect']);
        $banner->addData($nivoData);
		
		$this->setBanner($banner);
		return $this;
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
    
	public function getBanner(){
    	return $this->getData('banner');
    }
	public function getItems()
    {
    	return $this->getBanner()->getAllItems();
    }
}