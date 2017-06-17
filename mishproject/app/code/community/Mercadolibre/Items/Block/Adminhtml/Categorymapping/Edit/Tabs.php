<?php

class Mercadolibre_Items_Block_Adminhtml_Categorymapping_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('items_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('items')->__('Category Mapping'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('items')->__('Category Mapping'),
          'title'     => Mage::helper('items')->__('Category Mapping'),
          'content'   => $this->getLayout()->createBlock('items/adminhtml_categorymapping_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}