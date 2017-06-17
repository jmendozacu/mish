<?php

class VES_VendorsInventory_Block_Adjuststock extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adjuststock';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Adjust Stock');
        $this->_addButtonLabel = Mage::helper('vendorsinventory')->__('Create New Adjust Stock');
        parent::__construct();                                        
        Mage::dispatchEvent('adjust_stock_html_before', array('block' => $this));
    }

}
