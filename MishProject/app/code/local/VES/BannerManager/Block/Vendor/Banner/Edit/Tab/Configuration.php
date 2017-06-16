<?php

class VES_BannerManager_Block_Vendor_Banner_Edit_Tab_Configuration extends VES_BannerManager_Block_Adminhtml_Banner_Edit_Tab_Configuration
{
  protected function _prepareForm()
  {
      parent::_prepareForm();
      $this->getForm()->getElement('bannermanager_configuration')->removeField('store_id');
      return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
  }
}