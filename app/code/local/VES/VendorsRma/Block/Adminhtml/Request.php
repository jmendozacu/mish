<?php
class VES_VendorsRma_Block_Adminhtml_Request extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_request';
        $this->_blockGroup = 'vendorsrma';
        $this->_headerText = Mage::helper('vendorsrma')->__('All RMA');
        $this->_addButtonLabel = Mage::helper('vendorsrma')->__('Add New');
        parent::__construct();
    }
}