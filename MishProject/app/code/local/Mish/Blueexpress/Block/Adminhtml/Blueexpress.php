<?php
class Mish_Blueexpress_Block_Adminhtml_Blueexpress extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_blueexpress';
    $this->_blockGroup = 'blueexpress';
    $this->_headerText = Mage::helper('blueexpress')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('blueexpress')->__('Add Item');
    parent::__construct();
  }
}