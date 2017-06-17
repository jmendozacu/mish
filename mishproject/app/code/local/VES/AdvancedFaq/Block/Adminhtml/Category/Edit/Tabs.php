<?php

class OTTO_AdvancedFaq_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('category_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('advancedfaq')->__('Topic Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('advancedfaq')->__('Topic Information'),
          'title'     => Mage::helper('advancedfaq')->__('Topic Information'),
          'content'   => $this->getLayout()->createBlock('advancedfaq/adminhtml_category_edit_tab_form')->toHtml(),
      ));
      /*
      $this->addTab('meta_information_section', array(
      		'label'     => Mage::helper('advancedfaq')->__('Meta Information'),
      		'title'     => Mage::helper('advancedfaq')->__('Meta Information'),
      		'content'   => $this->getLayout()->createBlock('advancedfaq/adminhtml_category_edit_tab_meta')->toHtml(),
      ));
      */
      return parent::_beforeToHtml();
  }
}