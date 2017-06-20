<?php

class VES_VendorsInventory_Block_Notificationlog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
   public function __construct()
    {
        $this->_controller = 'notificationlog';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('inventorylowstock')->__('Notification Logs');
        
        parent::__construct();
        $this->_removeButton('add');
    }
}