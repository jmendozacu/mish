<?php

class VES_VendorsInventory_Block_Listadjuststock extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {                       
        $this->_controller = 'adjuststock_listadjuststock';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Manage Stock Adjustments');
        $this->_addButtonLabel = Mage::helper('vendorsinventory')->__('Add Stock Adjustment');
        parent::__construct();                                        
        if (!Mage::helper('vendorsinventory/adjuststock')->getWarehouse()) {
            $this->_removeButton('add');
        }
    }

}
