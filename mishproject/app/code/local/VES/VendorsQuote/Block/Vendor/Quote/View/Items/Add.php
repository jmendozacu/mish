<?php

class VES_VendorsQuote_Block_Vendor_Quote_View_Items_Add extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_search');
    }
    
    public function getHeaderText()
    {
        return Mage::helper('vendorsquote')->__('Please Select Products to Add');
    }
    
    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => Mage::helper('vendorsquote')->__('Add Selected Product(s) to Quote'),
            'onclick' => 'quotation.productGridAddSelected()',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }
    
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }
}