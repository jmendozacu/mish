<?php
class Mish_Personallogistic_Block_Adminhtml_Personallogistic extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_personallogistic';
    $this->_blockGroup = 'personallogistic';
    $this->_headerText = Mage::helper('personallogistic')->__('Personal Logistic');
    $this->_addButtonLabel = Mage::helper('personallogistic')->__('Add Item');
    parent::__construct();
    $this->_removeButton('add');
  }
}