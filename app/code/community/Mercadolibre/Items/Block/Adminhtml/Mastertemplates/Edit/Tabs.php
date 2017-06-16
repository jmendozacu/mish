<?php

class Mercadolibre_Items_Block_Adminhtml_Mastertemplates_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('mastertemplates_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('items')->__('Master Template'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('items')->__('Master Template'),
          'title'     => Mage::helper('items')->__('Master Template'),
          'content'   => $this->getLayout()->createBlock('items/adminhtml_mastertemplates_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}