<?php

class VES_VendorsQuote_Block_Vendor_Quote_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('group_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendors')->__('Quote View'));
	}
	
}