<?php
class VES_VendorsRma_Block_Vendor_Pending extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'vendor_pending';
        $this->_blockGroup = 'vendorsrma';
        $this->_headerText = Mage::helper('vendorsrma')->__('Pending RMA');
        $this->_addButtonLabel = Mage::helper('vendorsrma')->__('Add New');
        parent::__construct();
        $this->_removeButton("add");
    }
}