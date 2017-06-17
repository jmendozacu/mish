<?php
class Bluethink_Btslider_Block_Adminhtml_Btslider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_btslider';
    $this->_blockGroup = 'btslider';
    $this->_headerText = Mage::helper('btslider')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('btslider')->__('Add Item');
    parent::__construct();
  }
}