<?php

class VES_VendorsSubAccount_Block_Vendor_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorssubaccount_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorssubaccount')->__('Account Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorssubaccount')->__('Account Information'),
          'title'     => Mage::helper('vendorssubaccount')->__('Account Information'),
          'content'   => $this->getLayout()->createBlock('vendorssubaccount/vendor_profile_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}