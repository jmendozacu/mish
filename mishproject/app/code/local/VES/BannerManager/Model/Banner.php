<?php

class VES_BannerManager_Model_Banner extends Mage_Core_Model_Abstract
{
    const CACHE_TAG = "banner_manager";
	public function _construct()
    {
        parent::_construct();
        $this->_init('bannermanager/banner');
    }
    
    public function loadByIdentifier($identifier)
    {
    	$collection = Mage::getModel('bannermanager/banner')->getCollection()->addFieldToFilter('identifier',$identifier)->addStoreToFilter(Mage::helper("core")->getStoreId());
    	if(sizeof($collection)) foreach($collection as $banner) {return $banner;}
    	return false;
    }
    
    public function toOptionArray()
    {
    	$banners = array(
              array(
                  'value'     => '',
                  'label'     => Mage::helper('bannermanager')->__('Select a Banner'),
              )
        );
    	$bannerCollection = Mage::getModel('bannermanager/banner')->getCollection();
    	foreach($bannerCollection as $banner)
    	{
    		$banners[] = array('value'=>$banner->getId(),'label'=>$banner->getTitle());
    	}
    	return $banners;
    }
	public function getOptionArray()
    {
    	$banners = array();
    	$bannerCollection = Mage::getModel('bannermanager/banner')->getCollection();
    	foreach($bannerCollection as $banner)
    	{
    		$banners[$banner->getId()] = $banner->getTitle();
    	}
    	return $banners;
    }
    
    public function getAllItems()
    {
    	return Mage::getModel('bannermanager/item')->getCollection()->addFieldToFilter('banner_id',$this->getId())->setOrder('sort_order','asc');
    }
    
    public function getStores()
    {
    	
    }
}