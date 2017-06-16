<?php

class Mercadolibre_Items_Block_Adminhtml_Itemdetailprofile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('itemdetailprofile_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('items')->__('Profile Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('items')->__('Description Header'),
          'title'     => Mage::helper('items')->__('Description Header'),
          'content'   => $this->getLayout()->createBlock('items/adminhtml_itemdetailprofile_edit_tab_form')->toHtml(),
      ));
	  $this->addTab('form_section2', array(
          'label'     => Mage::helper('items')->__('Description Body'),
          'title'     => Mage::helper('items')->__('Description Body'),
          'content'   => $this->getLayout()->createBlock('items/adminhtml_itemdetailprofile_edit_tab_formbody')->toHtml(),
      ));
	  $this->addTab('form_section3', array(
          'label'     => Mage::helper('items')->__('Description Footer'),
          'title'     => Mage::helper('items')->__('Description Footer'),
          'content'   => $this->getLayout()->createBlock('items/adminhtml_itemdetailprofile_edit_tab_formfooter')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}