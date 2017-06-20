<?php

class VES_VendorsRma_Block_Adminhtml_Mestemplate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('template_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorsrma')->__('Template Information'));
  }

  protected function _beforeToHtml()
  {
  	
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorsrma')->__('Template Information'),
          'title'     => Mage::helper('vendorsrma')->__('Template Information'),
          'content'   => $this->getLayout()->createBlock('vendorsrma/adminhtml_mestemplate_edit_tab_information')->toHtml(),
      ));      
      return parent::_beforeToHtml();
  }
}