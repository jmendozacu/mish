<?php

class VES_BannerManager_Model_Source_Vendor_Banner
{
    public function toOptionArray($vendorId, $nullItem = true){
    	$banners = array();
    	if($nullItem){
        	$banners[] = array(
                  'value'     => '',
                  'label'     => Mage::helper('bannermanager')->__('-- Select a Banner --'),
            );
    	}
    	$bannerCollection = Mage::getModel('bannermanager/banner')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
    	foreach($bannerCollection as $banner)
    	{
    		$banners[] = array('value'=>$banner->getId(),'label'=>$banner->getTitle());
    	}
    	return $banners;
    }
    
	public function getOptionArray($vendorId, $nullItem = false){
    	$banners = array();
    	if($nullItem){
    		$banners[''] = Mage::helper('bannermanager')->__('-- Select a Banner --');
    	}
    	$bannerCollection = Mage::getModel('bannermanager/banner')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
    	foreach($bannerCollection as $banner)
    	{
    		$banners[$banner->getId()] = $banner->getTitle();
    	}
    	return $banners;
    }
}