<?php
class Mercadolibre_Items_Block_Adminhtml_Anstemplate extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_anstemplate';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Manage Answers Templates');
    parent::__construct();
	$this->removeButton('add');
  }
}