<?php

class VES_VendorsReview_Block_Adminhtml_Rating_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('rating_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorsreview')->__('Rating Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('main_section', array(
          'label'     => Mage::helper('vendorsreview')->__('Rating Information'),
          'title'     => Mage::helper('vendorsreview')->__('Rating Information'),
          'content'   => $this->getLayout()->createBlock('vendorsreview/adminhtml_rating_edit_tab_main')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}