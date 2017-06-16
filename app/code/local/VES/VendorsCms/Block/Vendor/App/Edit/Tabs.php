<?php

class VES_VendorsCms_Block_Vendor_App_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('app_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendorscms')->__('Frontend App Instance'));
	}
	protected function _beforeToHtml()
	{
		if(!$this->getRequest()->getParam('type') && !Mage::registry('cms_app')->getId()){
			$propertyBlock = $this->getLayout()->createBlock('vendorscms/vendor_app_edit_tab_setting','cms_app_edit_tab_setting');
			$this->addTab('property_section', $propertyBlock);
		}else{
			$propertyBlock = $this->getLayout()->createBlock('vendorscms/vendor_app_edit_tab_property','cms_app_edit_tab_property');
			$this->addTab('property_section', $propertyBlock);
			$type = Mage::registry('cms_app')->getId()?Mage::registry('cms_app')->getType():$this->getRequest()->getParam('type');
			Mage::dispatchEvent('ves_vendorscms_cms_app_add_tab_'.$type,array('tabs'=>$this));
			Mage::dispatchEvent('ves_vendorscms_cms_app_add_tab',array('tabs'=>$this));
		}
		return parent::_beforeToHtml();
	}
}