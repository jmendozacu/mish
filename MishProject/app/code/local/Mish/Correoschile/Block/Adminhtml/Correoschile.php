<?php
class Mish_Correoschile_Block_Adminhtml_Correoschile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_correoschile';
    $this->_blockGroup = 'correoschile';
    $this->_headerText = Mage::helper('correoschile')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('correoschile')->__('Add Item');
    parent::__construct();
  }
}