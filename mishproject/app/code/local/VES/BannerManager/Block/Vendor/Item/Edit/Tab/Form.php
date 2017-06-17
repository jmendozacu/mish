<?php

class VES_BannerManager_Block_Vendor_Item_Edit_Tab_Form extends VES_BannerManager_Block_Adminhtml_Item_Edit_Tab_Form
{
	protected function _prepareForm(){
		parent::_prepareForm();
		$vendorId = Mage::getSingleton('vendors/session')->getVendorId();
		$this->getForm()->getElement('banner_id')->setData('values',Mage::getModel('bannermanager/source_vendor_banner')->toOptionArray($vendorId));
		return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
	}
}