<?php

class VES_BannerManager_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('bannermanager_tabs');
     // //Mage::helper('ves_core');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('bannermanager')->__('Banner Information'));
  }

  protected function _beforeToHtml()
  {
  	  ////Mage::helper('ves_core');
      $this->addTab('form_section', array(
          'label'     => Mage::helper('bannermanager')->__('Banner Information'),
          'title'     => Mage::helper('bannermanager')->__('Banner Information'),
          'content'   => $this->getLayout()->createBlock('bannermanager/adminhtml_banner_edit_tab_form')->toHtml(),
      ));

      $this->addTab('configuration', array(
          'label'     => Mage::helper('bannermanager')->__('Configuration'),
          'title'     => Mage::helper('bannermanager')->__('Configuration'),
          'content'   => $this->getLayout()->createBlock('bannermanager/adminhtml_banner_edit_tab_configuration')->toHtml(),
      ));
      
      $this->addTab('grid_section', array(
          'label'     => Mage::helper('bannermanager')->__('Banner Items'),
          'title'     => Mage::helper('bannermanager')->__('Banner Items'),
          'content'   => $this->getLayout()->createBlock('bannermanager/adminhtml_banner_edit_tab_itemgrid')->toHtml(),
      ));
     $this->addTab('implement_code', array(
          'label'     => Mage::helper('bannermanager')->__('Implement Code'),
          'title'     => Mage::helper('bannermanager')->__('Implement Code For Banner Block'),
          'content'   => $this->getLayout()->createBlock('bannermanager/adminhtml_banner_edit_tab_implementcode')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}