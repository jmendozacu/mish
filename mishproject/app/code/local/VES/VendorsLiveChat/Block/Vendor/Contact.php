<?php
class VES_VendorsLiveChat_Block_Vendor_Contact extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'vendor_contact';
    $this->_blockGroup = 'vendorslivechat';
    $this->_headerText = Mage::helper('vendorslivechat')->__('Contact Manager');
    $this->_addButtonLabel = Mage::helper('vendorslivechat')->__('Add Contact');
    parent::__construct();
  }
}
