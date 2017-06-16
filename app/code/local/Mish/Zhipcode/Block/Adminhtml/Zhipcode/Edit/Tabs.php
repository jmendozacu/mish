<?php

class Mish_Zhipcode_Block_Adminhtml_Zhipcode_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('zhipcode_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('zhipcode')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('zhipcode')->__('Item Information'),
          'title'     => Mage::helper('zhipcode')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('zhipcode/adminhtml_zhipcode_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}