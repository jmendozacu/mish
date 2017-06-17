<?php
class VES_BannerManager_Block_Vendor_Widget_Image extends Mage_Core_Block_Template
{
    public function _toHtml()
    {
    	if(!Mage::getStoreConfig('bannermanager/config/enabled')) return;
    	//Mage::helper('ves_core');
    	if($img = $this->getBannerItem()){
    		$width 	= $this->getWidth();
    		$height = $this->getHeight();
    		$html = '<img src="'.$this->getImageUrl($img->getFilename()).'"'.($width?' width="'.$width.'"':'').($height?' width="'.$height.'"':'').' />';
    		return $html;
		}
    	return '';
    }

    public function getImageUrl($file_name)
    {
    	return Mage::getBaseUrl('media').$file_name;
    }
    
    public function getBannerItem()
    {
    	$itemId = $this->getImageId()?$this->getImageId():false;
    	//Mage::helper('ves_core');
    	if(!$itemId) return false;
    	$item = Mage::getModel('bannermanager/item')->getCollection()->addFieldToFilter('identifier',$itemId)->addFieldToFilter('vendor_id',$this->getVendor()->getId())->getFirstItem();
    	if(!$item) return false;
    	return($item->getStatus())?$item:false;
    }
}