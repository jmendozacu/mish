<?php

class VES_VendorsSubAccount_Block_Vendor_Role_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorssubaccount';
        $this->_controller = 'vendor_role';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorssubaccount')->__('Save Role'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorssubaccount')->__('Delete Role'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('role_data') && Mage::registry('role_data')->getId() ) {
            return Mage::helper('vendorssubaccount')->__("Edit Role '%s'", $this->htmlEscape(Mage::registry('role_data')->getRoleName()));
        } else {
            return Mage::helper('vendorssubaccount')->__('New Role');
        }
    }
}