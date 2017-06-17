<?php

class VES_VendorsReview_Block_Adminhtml_Review_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('review_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorsreview')->__('Review Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('main_section', array(
          'label'     => Mage::helper('vendorsreview')->__('Review Information'),
          'title'     => Mage::helper('vendorsreview')->__('Review Information'),
          'content'   => $this->getLayout()->createBlock('vendorsreview/adminhtml_review_edit_tab_main')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}