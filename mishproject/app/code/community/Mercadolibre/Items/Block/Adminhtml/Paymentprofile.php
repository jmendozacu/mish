<?php
class Mercadolibre_Items_Block_Adminhtml_Paymentprofile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_paymentprofile';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Payment Profiles');
   // $this->_addButtonLabel = Mage::helper('items')->__('Add Profile');
    parent::__construct();
	$this->removeButton('add');
  }
}