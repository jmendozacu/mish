<?php

class Mish_Shipit_Block_Adminhtml_Shipit_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('shipit_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('shipit')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('shipit')->__('Item Information'),
          'title'     => Mage::helper('shipit')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('shipit/adminhtml_shipit_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}