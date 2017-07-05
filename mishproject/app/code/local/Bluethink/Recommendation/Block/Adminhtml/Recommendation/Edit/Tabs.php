<?php

class Bluethink_Recommendation_Block_Adminhtml_Recommendation_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('recommendation_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('recommendation')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('recommendation')->__('Item Information'),
          'title'     => Mage::helper('recommendation')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('recommendation/adminhtml_recommendation_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}