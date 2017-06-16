<?php

class VES_VendorsRma_Block_Adminhtml_Type_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsrma';
        $this->_controller = 'adminhtml_type';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorsrma')->__('Save Type'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorsrma')->__('Delete Type'));
		
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $objId = $this->getRequest()->getParam($this->_objectId);
        $type = Mage::getModel("vendorsrma/type")->load($objId);
        
        if($type->getId() && $type->getData("is_delete")){
            $this->_removeButton('delete');
        }
        
        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendorsrma_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendorsrma_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendorsrma_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('type_data') && Mage::registry('type_data')->getId() ) {
            return Mage::helper('vendorsrma')->__("Edit RMA Type");
        } else {
            return Mage::helper('vendorsrma')->__('Add RMA Type');
        }
    }
}