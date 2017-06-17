<?php

class Mish_Personallogistic_Block_Adminhtml_Personallogisticorders_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('personallogistic_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('personallogistic')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('personallogistic')->__('Item Information'),
          'title'     => Mage::helper('personallogistic')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('personallogistic/adminhtml_personallogistic_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}