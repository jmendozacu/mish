<?php

class VES_VendorsLiveChat_Block_Vendor_Contact_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorslivechat';
        $this->_controller = 'vendor_contact';

        $this->_updateButton('save', 'label', Mage::helper('vendorslivechat')->__('Save Contact'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorslivechat')->__('Delete Contact'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendorslivechat_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendorslivechat_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendorslivechat_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('contact_data') && Mage::registry('contact_data')->getId() ) {
            return Mage::helper('vendorslivechat')->__("View Contact Of '%s'", $this->htmlEscape(Mage::registry('contact_data')->getName()));
        } else {
            return Mage::helper('vendorslivechat')->__('Add Contact');
        }
    }
}