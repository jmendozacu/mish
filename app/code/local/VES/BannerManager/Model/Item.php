<?php

class VES_BannerManager_Model_Item extends Mage_Core_Model_Abstract
{
	protected $_banner;
    public function _construct()
    {
        parent::_construct();
        $this->_init('bannermanager/item');
    }
    
    public function getBanner(){
    	if(!$this->_banner){
    		$this->_banner = Mage::getModel('bannermanager/banner')->load($this->getBannerId());
    	}
    	return $this->_banner;
    }
}