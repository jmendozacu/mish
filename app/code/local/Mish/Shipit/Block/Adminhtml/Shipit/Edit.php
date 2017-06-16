<?php

class Mish_Shipit_Block_Adminhtml_Shipit_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'shipit';
        $this->_controller = 'adminhtml_shipit';
        
        $this->_updateButton('save', 'label', Mage::helper('shipit')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('shipit')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('shipit_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'shipit_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'shipit_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('shipit_data') && Mage::registry('shipit_data')->getId() ) {
            return Mage::helper('shipit')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('shipit_data')->getTitle()));
        } else {
            return Mage::helper('shipit')->__('Add Item');
        }
    }
}