<?php

class VES_Commision_Block_Adminhtml_Commision_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'commision';
        $this->_controller = 'adminhtml_commision';
        
        $this->_updateButton('save', 'label', Mage::helper('commision')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('commision')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('commision_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'commision_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'commision_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('commision_data') && Mage::registry('commision_data')->getId() ) {
            return Mage::helper('commision')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('commision_data')->getTitle()));
        } else {
            return Mage::helper('commision')->__('Add Item');
        }
    }
}