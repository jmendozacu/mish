<?php

class VES_VendorsSubAccount_Block_Adminhtml_VendorsSubAccount_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorssubaccount';
        $this->_controller = 'adminhtml_vendorssubaccount';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorssubaccount')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorssubaccount')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendorssubaccount_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendorssubaccount_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendorssubaccount_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('vendorssubaccount_data') && Mage::registry('vendorssubaccount_data')->getId() ) {
            return Mage::helper('vendorssubaccount')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('vendorssubaccount_data')->getTitle()));
        } else {
            return Mage::helper('vendorssubaccount')->__('Add Item');
        }
    }
}