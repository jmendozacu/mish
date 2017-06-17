<?php

class VES_BannerManager_Block_Adminhtml_Item_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('item_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('bannermanager')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('bannermanager')->__('Item Information'),
          'title'     => Mage::helper('bannermanager')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('bannermanager/adminhtml_item_edit_tab_form')->toHtml(),
      ));
	  /*
     $this->addTab('form_customhtml', array(
          'label'     => Mage::helper('bannermanager')->__('Custom Html'),
          'title'     => Mage::helper('bannermanager')->__('Custom Html Banner Item'),
          'content'   => $this->getLayout()->createBlock('bannermanager/adminhtml_item_edit_tab_customhtml')->toHtml(),
      ));
	  */
      return parent::_beforeToHtml();
  }
}