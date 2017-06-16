<?php
class VES_BannerManager_Block_Adminhtml_Item extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_item';
    $this->_blockGroup = 'bannermanager';
    $this->_headerText = Mage::helper('bannermanager')->__('Items Manager');
    $this->_addButtonLabel = Mage::helper('bannermanager')->__('Add Item');
    parent::__construct();
  }
}