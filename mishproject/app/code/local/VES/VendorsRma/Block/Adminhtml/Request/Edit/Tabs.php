<?php

class VES_VendorsRma_Block_Adminhtml_Reason_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('reason_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorsrma')->__('Reason Information'));
  }

  protected function _beforeToHtml()
  {
      $request = Mage::registry("current_request");

      if($request->getData("order_id")){

      }
      else{
          $this->addTab('form_section', array(
              'label'     => Mage::helper('vendorsrma')->__('Basic RMA Information'),
              'title'     => Mage::helper('vendorsrma')->__('Basic RMA Information'),
              'content'   => $this->getLayout()->createBlock('vendorsrma/adminhtml_reason_edit_tab_basic')->toHtml(),
          ));
      }

     
      return parent::_beforeToHtml();
  }
}