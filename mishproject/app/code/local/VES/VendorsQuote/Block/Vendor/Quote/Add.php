<?php

class VES_VendorsQuote_Block_Vendor_Quote_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_mode        = 'add';
        $this->_blockGroup  = 'vendorsquote';
        $this->_controller  = 'vendor_quote';
        $this->_objectId    = 'quote_id';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorsquote')->__('Continue'));
        $this->_removeButton('reset');
        $quote = Mage::registry('quote');
    }

    public function getHeaderText()
    {
        return Mage::helper('vendorsquote')->__("Add new quote");
    }
}