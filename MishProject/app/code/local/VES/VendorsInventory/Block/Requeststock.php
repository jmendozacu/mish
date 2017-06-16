<?php

class VES_VendorsInventory_Block_Requeststock extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {        
        $this->_controller = 'requeststock';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Manage Stock Requests');
        parent::__construct();
        // $this->_removeButton('add');
        $this->_updateButton('add', 'label', Mage::helper('vendorsinventory')->__('Create Stock Request'));
        $this->_updateButton('add', 'onclick', 'setLocation(\'' . $this->getUrl('vendors/inventory_requeststock/new') . '\')');
    }

}
