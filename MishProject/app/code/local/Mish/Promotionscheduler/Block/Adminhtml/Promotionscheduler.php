<?php
class Mish_Promotionscheduler_Block_Adminhtml_Promotionscheduler extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_promotionscheduler';
    $this->_blockGroup = 'promotionscheduler';
    $this->_headerText = Mage::helper('promotionscheduler')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('promotionscheduler')->__('Add Item');
    parent::__construct();
  }
}