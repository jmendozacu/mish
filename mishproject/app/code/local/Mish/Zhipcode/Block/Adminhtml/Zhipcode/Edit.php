<?php

class Mish_Zhipcode_Block_Adminhtml_Zhipcode_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'zhipcode';
        $this->_controller = 'adminhtml_zhipcode';
        
        $this->_updateButton('save', 'label', Mage::helper('zhipcode')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('zhipcode')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('zhipcode_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'zhipcode_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'zhipcode_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('zhipcode_data') && Mage::registry('zhipcode_data')->getId() ) {
            return Mage::helper('zhipcode')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('zhipcode_data')->getTitle()));
        } else {
            return Mage::helper('zhipcode')->__('Add Item');
        }
    }
}