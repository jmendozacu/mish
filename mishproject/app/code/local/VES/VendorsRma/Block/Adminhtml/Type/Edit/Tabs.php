<?php

class VES_VendorsRma_Block_Adminhtml_Type_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('type_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorsrma')->__('Type Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorsrma')->__('Type Information'),
          'title'     => Mage::helper('vendorsrma')->__('Type Information'),
          'content'   => $this->getLayout()->createBlock('vendorsrma/adminhtml_type_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}