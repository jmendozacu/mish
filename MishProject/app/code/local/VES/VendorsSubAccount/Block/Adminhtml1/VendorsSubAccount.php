<?php
class VES_VendorsSubAccount_Block_Adminhtml_VendorsSubAccount extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vendorssubaccount';
    $this->_blockGroup = 'vendorssubaccount';
    $this->_headerText = Mage::helper('vendorssubaccount')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('vendorssubaccount')->__('Add Item');
    parent::__construct();
  }
}