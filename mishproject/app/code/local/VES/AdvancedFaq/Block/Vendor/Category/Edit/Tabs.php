<?php

class OTTO_AdvancedFaq_Block_Seller_Category_Edit_Tabs extends OTTO_AdvancedFaq_Block_Adminhtml_Category_Edit_Tabs
{
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
				'label'     => Mage::helper('advancedfaq')->__('Topic Information'),
				'title'     => Mage::helper('advancedfaq')->__('Topic Information'),
				'content'   => $this->getLayout()->createBlock('advancedfaq/seller_category_edit_tab_form')->toHtml(),
		));
		/*
		 $this->addTab('meta_information_section', array(
		 		'label'     => Mage::helper('advancedfaq')->__('Meta Information'),
		 		'title'     => Mage::helper('advancedfaq')->__('Meta Information'),
		 		'content'   => $this->getLayout()->createBlock('advancedfaq/adminhtml_category_edit_tab_meta')->toHtml(),
		 ));
		*/
		return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
	}
}