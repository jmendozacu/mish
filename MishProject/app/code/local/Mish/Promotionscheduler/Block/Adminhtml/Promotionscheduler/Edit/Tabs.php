<?php

class Mish_Promotionscheduler_Block_Adminhtml_Promotionscheduler_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('promotionscheduler_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('promotionscheduler')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('promotionscheduler')->__('Item Information'),
          'title'     => Mage::helper('promotionscheduler')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('promotionscheduler/adminhtml_promotionscheduler_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}