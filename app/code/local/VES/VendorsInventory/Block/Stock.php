<?php

class VES_VendorsInventory_Block_Stock extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'stock';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Manage Inventory');
        parent::__construct();
        $this->_removeButton('add');
    }

    protected function _prepareLayout() {
//        $this->_addButton('purchase_stock', array(
//            'label' => Mage::helper('vendorsinventory')->__('Request Stock'),
//            'onclick' => "setLocation('{$this->getUrl('vendors/inventory_requeststock/')}')",
//            'class' => 'edit'
//        ));
//        $this->_addButton('send_stock', array(
//            'label' => Mage::helper('vendorsinventory')->__('Send Stock'),
//            'onclick' => "setLocation('{$this->getUrl('vendors/inventory_sendstock/')}')",
//            'class' => 'add'
//        ));
        $this->_addButton('add_physical_stock', array(
            'label' => Mage::helper('vendorsinventory')->__('Physical Stock-taking'),
            'onclick' => "setLocation('{$this->getUrl('vendors/inventory_physicalstocktaking/')}')",
            'class' => 'edit'
        ));
        $this->_addButton('adjust_stock', array(
            'label' => Mage::helper('vendorsinventory')->__('Adjust Stock'),
            'onclick' => "setLocation('{$this->getUrl('vendors/inventory_adjuststock')}')",
            'class' => 'back'
        ));        
//        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/catalog_product_grid', 'product.grid'));
        return parent::_prepareLayout();
    }

}
