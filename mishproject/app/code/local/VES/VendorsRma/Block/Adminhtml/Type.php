<?php
class VES_VendorsRma_Block_Adminhtml_Type extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_type';
        $this->_blockGroup = 'vendorsrma';
        $this->_headerText = Mage::helper('vendorsrma')->__('Manage Types');
        $this->_addButtonLabel = Mage::helper('vendorsrma')->__('Add New');
        parent::__construct();
    }
}