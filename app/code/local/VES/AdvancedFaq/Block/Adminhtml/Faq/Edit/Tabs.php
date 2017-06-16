<?php

class OTTO_AdvancedFaq_Block_Adminhtml_Faq_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('faq_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('advancedfaq')->__('Faq Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('advancedfaq')->__('Faq Information'),
          'title'     => Mage::helper('advancedfaq')->__('Faq Information'),
          'content'   => $this->getLayout()->createBlock('advancedfaq/adminhtml_faq_edit_tab_form')->toHtml(),
      ));

     if($this->getRequest()->getParam('id')){
	     $this->addTab('rating_information_section', array(
	     		'label'     => Mage::helper('advancedfaq')->__('Rating'),
	     		'title'     => Mage::helper('advancedfaq')->__('Rating'),
	     		'content'   => $this->getLayout()->createBlock('advancedfaq/adminhtml_faq_edit_tab_rating')->toHtml(),
	     ));
     }
      return parent::_beforeToHtml();
  }
}