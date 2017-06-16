<?php
class VES_BannerManager_Block_Vendor_Item extends VES_BannerManager_Block_Adminhtml_Item
{
	public function __construct(){
    	parent::__construct();
    	$this->_controller = 'vendor_item';
	}
}