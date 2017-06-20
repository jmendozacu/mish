<?php
class Mish_Zhipcode_Block_Adminhtml_Zhipcode extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_zhipcode';
    $this->_blockGroup = 'zhipcode';
    $this->_headerText = Mage::helper('zhipcode')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('zhipcode')->__('Add Item');
    parent::__construct();
  }
}