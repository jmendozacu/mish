<?php

class Mercadolibre_Items_Block_Adminhtml_Categorymapping_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'items';
        $this->_controller = 'adminhtml_categorymapping';
        
        $this->_updateButton('save', 'label', Mage::helper('items')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('items')->__('Delete Item'));
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('items_data') && Mage::registry('items_data')->getId() ) {
            return Mage::helper('items')->__("Edit Category Mapping '%s'", $this->htmlEscape(Mage::registry('items_data')->getTitle()));
        } else {
            return Mage::helper('items')->__('Category Mapping');
        }
    }
}