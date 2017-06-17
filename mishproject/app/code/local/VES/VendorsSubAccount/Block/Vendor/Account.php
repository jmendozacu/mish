<?php
class VES_VendorsSubAccount_Block_Vendor_Account extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'vendor_account';
		$this->_blockGroup = 'vendorssubaccount';
		$this->_headerText = Mage::helper('vendorssubaccount')->__('Users');
		$this->_addButtonLabel = Mage::helper('vendorssubaccount')->__('Add New User');
		parent::__construct();
	}
}