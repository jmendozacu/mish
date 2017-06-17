<?php

class VES_VendorsCms_Block_Vendor_Block_Edit extends Mage_Adminhtml_Block_Cms_Block_Edit{
	public function __construct(){
		parent::__construct();
		$this->_controller = 'vendor_block';
		$this->_blockGroup = 'vendorscms';
		$this->updateButton('saveandcontinue', 'class','save-and-continue');
	}
}