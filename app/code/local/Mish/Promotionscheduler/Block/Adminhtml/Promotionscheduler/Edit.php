<?php

class Mish_Promotionscheduler_Block_Adminhtml_Promotionscheduler_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'promotionscheduler';
        $this->_controller = 'adminhtml_promotionscheduler';
        
        $this->_updateButton('save', 'label', Mage::helper('promotionscheduler')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('promotionscheduler')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('promotionscheduler_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'promotionscheduler_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'promotionscheduler_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('promotionscheduler_data') && Mage::registry('promotionscheduler_data')->getId() ) {
            return Mage::helper('promotionscheduler')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('promotionscheduler_data')->getTitle()));
        } else {
            return Mage::helper('promotionscheduler')->__('Add Item');
        }
    }
}