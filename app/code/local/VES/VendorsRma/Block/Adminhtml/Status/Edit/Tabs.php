<?php

class VES_VendorsRma_Block_Adminhtml_Status_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('type_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorsrma')->__('Status Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorsrma')->__('Main Information'),
          'title'     => Mage::helper('vendorsrma')->__('Main Information'),
          'content'   => $this->getLayout()->createBlock('vendorsrma/adminhtml_status_edit_tab_form')->toHtml(),
      ));

      $this->addTab('form_template', array(
          'label'     => Mage::helper('vendorsrma')->__('Store Templates'),
          'title'     => Mage::helper('vendorsrma')->__('Store Templates'),
          'content'   => $this->getLayout()->createBlock('vendorsrma/adminhtml_status_edit_tab_template')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}