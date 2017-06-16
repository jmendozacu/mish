<?php

class VES_BannerManager_Block_Vendor_Item_Edit_Tabs extends VES_BannerManager_Block_Adminhtml_Item_Edit_Tabs{
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();
		$this->setTabData('form_section', 'content', $this->getLayout()->createBlock('bannermanager/vendor_item_edit_tab_form')->toHtml());
		$this->addTab('vendor_implement_code', array(
          	'label'     => Mage::helper('bannermanager')->__('Implement Code'),
          	'title'     => Mage::helper('bannermanager')->__('Implement Code For Banner Block'),
          	'content'   => $this->getLayout()->createBlock('bannermanager/vendor_item_edit_tab_implementcode')->toHtml(),
      	));
		return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
	}
}