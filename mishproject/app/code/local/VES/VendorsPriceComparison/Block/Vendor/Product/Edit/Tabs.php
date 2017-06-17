<?php

class VES_VendorsPriceComparison_Block_Vendor_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('vendors_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendors')->__('Product Price Comparison'));
	}
	
	protected function _beforeToHtml(){
		$this->addTab('main_section', array(
			'label'     => Mage::helper('vendors')->__('Main'),
			'title'     => Mage::helper('vendors')->__('Main'),
			'content'   => $this->getLayout()->createBlock('pricecomparison/vendor_product_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}