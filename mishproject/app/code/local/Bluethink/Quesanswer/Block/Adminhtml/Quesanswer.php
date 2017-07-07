<?php
class Bluethink_Quesanswer_Block_Adminhtml_Quesanswer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_quesanswer';
    $this->_blockGroup = 'quesanswer';
    $this->_headerText = Mage::helper('quesanswer')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('quesanswer')->__('Add Item');
    parent::__construct();
  }
}