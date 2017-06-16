<?php

class VES_VendorsRma_Block_Adminhtml_Reason_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsrma';
        $this->_controller = 'adminhtml_reason';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorsrma')->__('Save Reason'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorsrma')->__('Delete Reason'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendorsrma_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendorsrma_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendorsrma_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('reason_data') && Mage::registry('reason_data')->getId() ) {
            return Mage::helper('vendorsrma')->__("Edit RMA Reason");
        } else {
            return Mage::helper('vendorsrma')->__('Add RMA Reason');
        }
    }
}