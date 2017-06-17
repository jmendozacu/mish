<?php

class VES_VendorsVacation_Block_Adminhtml_Vacation_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vacation_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorsvacation')->__('Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorsvacation')->__('Information'),
          'title'     => Mage::helper('vendorsvacation')->__('Information'),
          'content'   => $this->getLayout()->createBlock('vendorsvacation/adminhtml_vacation_edit_tab_form')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}