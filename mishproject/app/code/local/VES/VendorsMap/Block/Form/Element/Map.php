<?php
class VES_VendorsMap_Block_Form_Element_Map extends Varien_Data_Form_Element_Abstract
{
	public function __construct($attributes=array())
	{
		parent::__construct($attributes);
		$this->setType('map');
	}
	public function getHtml()
	{
		$block = Mage::app()->getLayout()->createBlock("vendorsmap/adminhtml_account_html");
		if(Mage::registry('vendors_data')){
			$block->setVendors(Mage::registry('vendors_data'));
		}
		return $block->toHtml();
	}
}