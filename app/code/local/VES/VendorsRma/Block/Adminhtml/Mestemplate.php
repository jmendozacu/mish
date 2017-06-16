<?php

class VES_VendorsRma_Block_Adminhtml_Mestemplate extends Mage_Adminhtml_Block_Widget_Grid_Container
{
     public function __construct()
      {
        $this->_controller = 'adminhtml_mestemplate';
        $this->_blockGroup = 'vendorsrma';
        $this->_headerText = Mage::helper('vendorsrma')->__('RMA - Message Templates');
        $this->_addButtonLabel = Mage::helper('vendorsrma')->__('Add New');
        parent::__construct();
      }
}