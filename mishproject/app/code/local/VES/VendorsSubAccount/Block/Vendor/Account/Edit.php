<?php

class VES_VendorsSubAccount_Block_Vendor_Account_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorssubaccount';
        $this->_controller = 'vendor_account';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorssubaccount')->__('Save User'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorssubaccount')->__('Delete User'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('vendorssubaccount_data') && Mage::registry('vendorssubaccount_data')->getId() ) {
            return Mage::helper('vendorssubaccount')->__("Edit User '%s'", $this->htmlEscape(Mage::registry('vendorssubaccount_data')->getUsername()));
        } else {
            return Mage::helper('vendorssubaccount')->__('New User');
        }
    }
}