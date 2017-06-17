<?php
class VES_VendorsCategory_Block_Vendor_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'vendor_category';
		$this->_blockGroup = 'vendorscategory';
		$this->_headerText = Mage::helper('vendorscategory')->__('Category Manager');
		$this->_addButtonLabel = Mage::helper('vendorscategory')->__('New Category');
		parent::__construct();
	}
}