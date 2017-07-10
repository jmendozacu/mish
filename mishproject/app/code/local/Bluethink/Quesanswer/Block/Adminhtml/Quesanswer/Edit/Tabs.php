<?php

class Bluethink_Quesanswer_Block_Adminhtml_Quesanswer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('quesanswer_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('quesanswer')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('quesanswer')->__('Item Information'),
          'title'     => Mage::helper('quesanswer')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('quesanswer/adminhtml_quesanswer_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}