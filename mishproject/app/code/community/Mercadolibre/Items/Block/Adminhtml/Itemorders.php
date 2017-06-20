<?php
class Mercadolibre_Items_Block_Adminhtml_Itemorders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_Itemorders';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Order Manager');
   // $this->_addButtonLabel = Mage::helper('items')->__('Add Order');
   $this->_addButton('saveastemplate', array(
	'label'     => Mage::helper('adminhtml')->__('Refresh'),
	'onclick'   => 'location.reload();',
), -100);

    parent::__construct();
	$this->removebutton('add');
	$this->setTemplate('items/orders/contentMlOrderListing.phtml');
  }
}