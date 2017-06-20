<?php
class VES_BannerManager_Block_Vendor_Widget_Banner extends VES_BannerManager_Block_Banner
{

    
    public function getBanner()
    {
    	$banner_id = $this->getBannerId()?$this->getBannerId():false;
		
		//echo $banner_id;exit;
		//echo $this->getVendor()->getId();exit;
    	//Mage::helper('ves_core');
    	if(!$banner_id) return false;
    	$banner = Mage::getModel('bannermanager/banner')->getCollection()
	    	->addFieldToFilter('identifier',$banner_id)
	    	->addFieldToFilter('vendor_id',$this->getVendor()->getId())
	    	->getFirstItem();
			
		$nivoData = unserialize($banner->getNivoOptions());
        $nivoData['nivoeffect'] = explode(',', $nivoData['effect']);

        unset($nivoData['effect']);
        $banner->addData($nivoData);
		//var_dump($banner->getData());exit;
    	if(!$banner) return false;
    	return($banner->getStatus())?$banner:false;
    }

}