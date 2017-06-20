<?php
class VES_VendorsQuote_Block_Vendor_Quote extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'vendor_quote';
        $this->_blockGroup = 'vendorsquote';
        $this->_headerText = Mage::helper('vendors')->__('Quotations');
        $this->_addButtonLabel = Mage::helper('vendors')->__('Create New Quote');
        parent::__construct();
    }
}