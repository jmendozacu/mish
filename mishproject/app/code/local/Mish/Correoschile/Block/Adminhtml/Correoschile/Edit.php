<?php

class Mish_Correoschile_Block_Adminhtml_Correoschile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'correoschile';
        $this->_controller = 'adminhtml_correoschile';
        
        $this->_updateButton('save', 'label', Mage::helper('correoschile')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('correoschile')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('correoschile_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'correoschile_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'correoschile_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('correoschile_data') && Mage::registry('correoschile_data')->getId() ) {
            return Mage::helper('correoschile')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('correoschile_data')->getTitle()));
        } else {
            return Mage::helper('correoschile')->__('Add Item');
        }
    }
}