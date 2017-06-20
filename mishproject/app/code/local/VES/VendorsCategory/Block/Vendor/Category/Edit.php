<?php

class VES_VendorsCategory_Block_Vendor_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'vendor_category';
        $this->_blockGroup = 'vendorscategory';
        $this->_mode = 'edit';

        parent::__construct();

        $this->setTemplate('ves_vendorscategory/vendor/category/edit.phtml');
    }

/*    public function getHeaderText()
    {
        if( Mage::registry('category_data') && Mage::registry('category_data')->getId() ) {
            return Mage::helper('vendorscategory')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('category_data')->getName()));
        } else {
            return Mage::helper('vendorscategory')->__('Add Category');
        }
    }*/
    
}