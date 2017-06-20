<?php
class VES_VendorsQuote_Block_Adminhtml_Quote extends VES_VendorsQuote_Block_Vendor_Quote
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_quote';
        $this->_removeButton('add');
        return $this;
    }
}