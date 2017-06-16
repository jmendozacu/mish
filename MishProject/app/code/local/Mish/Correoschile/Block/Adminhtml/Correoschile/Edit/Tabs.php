<?php

class Mish_Correoschile_Block_Adminhtml_Correoschile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('correoschile_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('correoschile')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('correoschile')->__('Item Information'),
          'title'     => Mage::helper('correoschile')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('correoschile/adminhtml_correoschile_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}