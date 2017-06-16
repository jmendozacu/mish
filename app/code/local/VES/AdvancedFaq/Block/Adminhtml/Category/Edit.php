<?php

class OTTO_AdvancedFaq_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'advancedfaq';
        $this->_controller = 'adminhtml_category';
        
        $this->_updateButton('save', 'label', Mage::helper('advancedfaq')->__('Save Topic'));
        $this->_updateButton('delete', 'label', Mage::helper('advancedfaq')->__('Delete Topic'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('kbase_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'kbase_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'kbase_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('category_data') && Mage::registry('category_data')->getId() ) {
            return Mage::helper('advancedfaq')->__("Edit Topic '%s'", $this->htmlEscape(Mage::registry('category_data')->getTitle()));
        } else {
            return Mage::helper('advancedfaq')->__('Add Topic');
        }
    }
}