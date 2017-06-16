<?php

class VES_BannerManager_Block_Adminhtml_Item_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'bannermanager';
        $this->_controller = 'adminhtml_item';
        
        $this->_updateButton('save', 'label', Mage::helper('bannermanager')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('bannermanager')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('bannermanager_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'bannermanager_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'bannermanager_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('bannermanager_data') && Mage::registry('bannermanager_data')->getId() ) {
            return Mage::helper('bannermanager')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('bannermanager_data')->getTitle()));
        } else {
            return Mage::helper('bannermanager')->__('Add Item');
        }
    }
}