<?php
class VES_VendorsCatalogSearch_Block_Form extends Mage_Core_Block_Template{
	protected function _toHtml(){
		if(!Mage::helper('vendorsconfig')->getVendorConfig('catalogsearch/config/enabled',Mage::registry('vendor')->getId())) return '';
		return parent::_toHtml();
	}
}
