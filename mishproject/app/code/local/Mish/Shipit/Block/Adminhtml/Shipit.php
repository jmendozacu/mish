<?php
class Mish_Shipit_Block_Adminhtml_Shipit extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_shipit';
    $this->_blockGroup = 'shipit';
    $this->_headerText = Mage::helper('shipit')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('shipit')->__('Add Item');
    parent::__construct();
  }
}