<?php

class VES_VendorsSubAccount_Block_Adminhtml_VendorsSubAccount_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorssubaccount_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorssubaccount')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorssubaccount')->__('Item Information'),
          'title'     => Mage::helper('vendorssubaccount')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('vendorssubaccount/adminhtml_vendorssubaccount_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}