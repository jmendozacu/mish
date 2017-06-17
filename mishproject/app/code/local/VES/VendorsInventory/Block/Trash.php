<?php
class VES_VendorsInventory_Block_Trash extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'trash';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('inventorypurchasing')->__('Deleted Purchase Order Manager');
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('Back', array(
            'label' => Mage::helper('inventorypurchasing')->__('Back'),
            'onclick' => "setLocation('{$this->getUrl('*/*/index')}')",
            'class' => 'back',
        ), -110);
    }
}