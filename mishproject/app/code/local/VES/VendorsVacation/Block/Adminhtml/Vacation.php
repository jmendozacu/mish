<?php
class VES_VendorsVacation_Block_Adminhtml_Vacation extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vacation';
    $this->_blockGroup = 'vendorsvacation';
    $this->_headerText = Mage::helper('vendorsvacation')->__('Vendors are in vacation.');

     parent::__construct();
     $this->_removeButton('add');
  }
}