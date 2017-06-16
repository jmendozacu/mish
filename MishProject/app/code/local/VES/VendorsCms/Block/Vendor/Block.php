<?php
class VES_VendorsCms_Block_Vendor_Block extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'vendor_block';
		$this->_blockGroup = 'vendorscms';
		$this->_headerText = $this->getTitle();
		$this->_addButtonLabel = Mage::helper('vendorscms')->__('Add New Block');
		parent::__construct();
	}
	
	/**
	 * Get Title
	 */
	public function getTitle(){
		return Mage::helper('vendorscms')->__('Static Blocks');
	}
}