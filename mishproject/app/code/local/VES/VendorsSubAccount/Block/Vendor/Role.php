<?php
class VES_VendorsSubAccount_Block_Vendor_Role extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'vendor_role';
		$this->_blockGroup = 'vendorssubaccount';
		$this->_headerText = Mage::helper('vendorssubaccount')->__('Roles');
		$this->_addButtonLabel = Mage::helper('vendorssubaccount')->__('Add New Role');
		parent::__construct();
	}
}