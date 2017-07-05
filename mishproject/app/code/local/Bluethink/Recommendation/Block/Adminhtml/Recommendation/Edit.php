<?php

class Bluethink_Recommendation_Block_Adminhtml_Recommendation_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'recommendation';
        $this->_controller = 'adminhtml_recommendation';
        
        $this->_updateButton('save', 'label', Mage::helper('recommendation')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('recommendation')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('recommendation_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'recommendation_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'recommendation_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('recommendation_data') && Mage::registry('recommendation_data')->getId() ) {
            return Mage::helper('recommendation')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('recommendation_data')->getTitle()));
        } else {
            return Mage::helper('recommendation')->__('Add Item');
        }
    }
}