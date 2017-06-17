<?php
class Mercadolibre_Items_Block_Adminhtml_Feedbacks extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_feedbacks';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Feedback');    
	$this->_addButton('saveastemplate', array(
	'label'     => Mage::helper('adminhtml')->__('Refresh'),
	'onclick'   => 'location.reload();',), -100);
    parent::__construct();
	$this->removeButton('add');
  }
}