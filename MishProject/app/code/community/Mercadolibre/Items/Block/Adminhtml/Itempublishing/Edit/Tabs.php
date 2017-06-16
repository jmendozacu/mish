<?php

class Mercadolibre_Items_Block_Adminhtml_Itempublishing_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('items_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('items')->__('Publish Item'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('items')->__('Publish Item'),
          'title'     => Mage::helper('items')->__('Publish Item'),
          'content'   => $this->getLayout()->createBlock('items/adminhtml_itempublishing_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}