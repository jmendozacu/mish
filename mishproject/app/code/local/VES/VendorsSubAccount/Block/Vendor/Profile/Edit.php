<?php

class VES_VendorsSubAccount_Block_Vendor_Profile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorssubaccount';
        $this->_controller = 'vendor_profile';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorssubaccount')->__('Save Account'));
    }

    public function getHeaderText()
    {
        return Mage::helper('vendorssubaccount')->__('My Account');
    }
}