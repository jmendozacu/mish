<?php

class VES_Commision_Block_Adminhtml_Managecommision_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('commision_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('commision')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('commision')->__('Item Information'),
          'title'     => Mage::helper('commision')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('commision/adminhtml_managecommision_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}