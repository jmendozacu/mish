<?php

class VES_VendorsInventory_Block_Listphysicalstocktaking extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    { 
        $this->_controller = 'physicalstocktaking_listphysicalstocktaking';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Manage Physical Stocktaking');
        $this->_addButtonLabel = Mage::helper('vendorsinventory')->__('Add New Physical Stocktaking');
        parent::__construct();
        if(!Mage::helper('vendorsinventory/physicalstock')->getPhysicalWarehouseByAdmin()){
            $this->_removeButton('add');
        }
    }
}