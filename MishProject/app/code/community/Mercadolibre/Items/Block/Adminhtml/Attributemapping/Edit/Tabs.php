<?php

class Mercadolibre_Items_Block_Adminhtml_Attributemapping_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('items_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('items')->__('Attribute Mapping'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('items')->__('Attribute Mapping'),
          'title'     => Mage::helper('items')->__('Attribute Mapping'),
          'content'   => $this->getLayout()->createBlock('items/adminhtml_attributemapping_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}