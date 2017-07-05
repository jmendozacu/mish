<?php
class Bluethink_Recommendation_Block_Adminhtml_Recommendation extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_recommendation';
    $this->_blockGroup = 'recommendation';
    $this->_headerText = Mage::helper('recommendation')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('recommendation')->__('Add Item');
    parent::__construct();
  }
}