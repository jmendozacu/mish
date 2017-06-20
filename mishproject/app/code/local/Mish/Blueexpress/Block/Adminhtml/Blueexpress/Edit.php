<?php

class Mish_Blueexpress_Block_Adminhtml_Blueexpress_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'blueexpress';
        $this->_controller = 'adminhtml_blueexpress';
        
        $this->_updateButton('save', 'label', Mage::helper('blueexpress')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('blueexpress')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('blueexpress_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'blueexpress_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'blueexpress_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('blueexpress_data') && Mage::registry('blueexpress_data')->getId() ) {
            return Mage::helper('blueexpress')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('blueexpress_data')->getTitle()));
        } else {
            return Mage::helper('blueexpress')->__('Add Item');
        }
    }
}