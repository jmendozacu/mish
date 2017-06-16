<?php

class Mish_Blueexpress_Block_Adminhtml_Blueexpress_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('blueexpress_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('blueexpress')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('blueexpress')->__('Item Information'),
          'title'     => Mage::helper('blueexpress')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('blueexpress/adminhtml_blueexpress_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}