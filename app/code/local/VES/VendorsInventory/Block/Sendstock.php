<?php

class VES_VendorsInventory_Block_Sendstock extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'sendstock';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Manage Stock Sending');
        $this->_addButtonLabel = Mage::helper('vendorsinventory')->__('Create Stock Sending');
        parent::__construct();
    }

}
