<?php

class OTTO_AdvancedFaq_Block_Seller_Faq_Edit_Tabs extends OTTO_AdvancedFaq_Block_Adminhtml_Faq_Edit_Tabs
{
  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('advancedfaq')->__('Faq Information'),
          'title'     => Mage::helper('advancedfaq')->__('Faq Information'),
          'content'   => $this->getLayout()->createBlock('advancedfaq/seller_faq_edit_tab_form')->toHtml(),
      ));
      return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
  }

}