<?php
class VES_Commision_Block_Adminhtml_ManageCommision extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_managecommision';
    $this->_blockGroup = 'commision';
    $this->_headerText = Mage::helper('commision')->__('Commissions');
    $this->_addButtonLabel = Mage::helper('commision')->__('Add Item');
    parent::__construct();
    $this->_removeButton('add');
  }
}