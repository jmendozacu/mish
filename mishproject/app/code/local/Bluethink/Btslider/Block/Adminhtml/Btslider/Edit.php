<?php

class Bluethink_Btslider_Block_Adminhtml_Btslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'btslider';
        $this->_controller = 'adminhtml_btslider';
        
        $this->_updateButton('save', 'label', Mage::helper('btslider')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('btslider')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('btslider_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'btslider_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'btslider_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('btslider_data') && Mage::registry('btslider_data')->getId() ) {
            return Mage::helper('btslider')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('btslider_data')->getTitle()));
        } else {
            return Mage::helper('btslider')->__('Add Item');
        }
    }
}