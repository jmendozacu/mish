<?php

class VES_VendorsSubAccount_Block_Vendor_Role_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorssubaccount_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorssubaccount')->__('Role Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorssubaccount')->__('Role Info'),
          'title'     => Mage::helper('vendorssubaccount')->__('Role Info'),
          'content'   => $this->getLayout()->createBlock('vendorssubaccount/vendor_role_edit_tab_form')->toHtml(),
      ));
     $this->addTab('resources_section', array(
          'label'     => Mage::helper('vendorssubaccount')->__('Role Resources'),
          'title'     => Mage::helper('vendorssubaccount')->__('Role Resources'),
          'content'   => $this->getLayout()->createBlock('vendorssubaccount/vendor_role_edit_tab_resource')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}