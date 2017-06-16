<?php

class Mercadolibre_Items_Block_Adminhtml_Shippingprofile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('shippingprofile_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('items')->__('Shipping Template'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('items')->__('Shipping Template'),
          'title'     => Mage::helper('items')->__('Shipping Template'),
          'content'   => $this->getLayout()->createBlock('items/adminhtml_shippingprofile_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}