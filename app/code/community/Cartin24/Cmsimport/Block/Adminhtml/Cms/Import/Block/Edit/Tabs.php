<?php

class Cartin24_Cmsimport_Block_Adminhtml_Cms_Import_Block_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('cmsimport_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('cmsimport')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('cmsimport')->__('Item Information'),
          'title'     => Mage::helper('cmsimport')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('cmsimport/adminhtml_cms_import_block_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
