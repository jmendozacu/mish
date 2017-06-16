<?php

class Bluethink_Btslider_Block_Adminhtml_Btslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('btslider_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('btslider')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('btslider')->__('Item Information'),
          'title'     => Mage::helper('btslider')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('btslider/adminhtml_btslider_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}