<?php
class VES_VendorsLiveChat_Block_Vendor_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'vendor_history';
        $this->_blockGroup = 'vendorslivechat';
        $this->_headerText = Mage::helper('vendorslivechat')->__('History Manager');
        $this->_addButtonLabel = Mage::helper('vendorslivechat')->__('Add Contact');
        parent::__construct();
        $this->_removeButton("add");
    }
}
