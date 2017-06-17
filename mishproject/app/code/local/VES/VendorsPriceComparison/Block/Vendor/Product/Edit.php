<?php

class VES_VendorsPriceComparison_Block_Vendor_Product_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'pricecomparison';
        $this->_controller = 'vendor_product';
        
        $this->_updateButton('save', 'label', Mage::helper('pricecomparison')->__('Save'));
        $this->_removeButton('reset');
        $this->_removeButton('back');
    }

    public function getHeaderText(){
    	return Mage::helper('pricecomparison')->__("Sell Your Product");
    }
}