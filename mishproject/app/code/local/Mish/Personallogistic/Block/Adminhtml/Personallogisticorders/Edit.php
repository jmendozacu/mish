<?php

class Mish_Personallogistic_Block_Adminhtml_Personallogisticorders_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'personallogistic';
        $this->_controller = 'adminhtml_personallogisticorders';
        
        $this->_updateButton('save', 'label', Mage::helper('personallogistic')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('personallogistic')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('personallogistic_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'personallogistic_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'personallogistic_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('personallogistic_data') && Mage::registry('personallogistic_data')->getId() ) {
            return Mage::helper('personallogistic')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('personallogistic_data')->getTitle()));
        } else {
            return Mage::helper('personallogistic')->__('Add Item');
        }
    }
}