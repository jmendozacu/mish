<?php

class VES_BannerManager_Block_Vendor_Banner_Edit_Tabs extends VES_BannerManager_Block_Adminhtml_Banner_Edit_Tabs
{
  protected function _beforeToHtml()
  {
      parent::_beforeToHtml();
      $this->removeTab('implement_code');
      $this->removeTab('grid_section');
      $this->setTabData('configuration', 'content', $this->getLayout()->createBlock('bannermanager/vendor_banner_edit_tab_configuration')->toHtml());
      $this->addTab('vendor_grid_section', array(
          'label'     => Mage::helper('bannermanager')->__('Banner Items'),
          'title'     => Mage::helper('bannermanager')->__('Banner Items'),
          'content'   => $this->getLayout()->createBlock('bannermanager/vendor_banner_edit_tab_itemgrid')->toHtml(),
      ));
      $this->addTab('vendor_implement_code', array(
          'label'     => Mage::helper('bannermanager')->__('Implement Code'),
          'title'     => Mage::helper('bannermanager')->__('Implement Code For Banner Block'),
          'content'   => $this->getLayout()->createBlock('bannermanager/vendor_banner_edit_tab_implementcode')->toHtml(),
      ));
      return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
  }
}