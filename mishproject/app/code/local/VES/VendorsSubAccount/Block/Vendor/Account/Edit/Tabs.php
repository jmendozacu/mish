<?php

class VES_VendorsSubAccount_Block_Vendor_Account_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorssubaccount_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorssubaccount')->__('User Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorssubaccount')->__('User Info'),
          'title'     => Mage::helper('vendorssubaccount')->__('User Info'),
          'content'   => $this->getLayout()->createBlock('vendorssubaccount/vendor_account_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}