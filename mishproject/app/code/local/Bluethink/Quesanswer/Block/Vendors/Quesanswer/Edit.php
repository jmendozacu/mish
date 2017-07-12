<?php

class Bluethink_Quesanswer_Block_Vendors_Quesanswer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'quesanswer';
        $this->_controller = 'adminhtml_quesanswer';
        
        $this->_updateButton('save', 'label', Mage::helper('quesanswer')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('quesanswer')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('quesanswer_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'quesanswer_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'quesanswer_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('quesanswer_data') && Mage::registry('quesanswer_data')->getId() ) {
            return Mage::helper('quesanswer')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('quesanswer_data')->getTitle()));
        } else {
            return Mage::helper('quesanswer')->__('Add Item');
        }
    }
}