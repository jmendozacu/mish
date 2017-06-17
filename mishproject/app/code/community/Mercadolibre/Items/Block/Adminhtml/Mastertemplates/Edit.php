<?php

class Mercadolibre_Items_Block_Adminhtml_Mastertemplates_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'items';
        $this->_controller = 'adminhtml_mastertemplates';
        
        $this->_updateButton('save', 'label', Mage::helper('items')->__('Save Template'));
        $this->_updateButton('delete', 'label', Mage::helper('items')->__('Delete Template'));
    }

    public function getHeaderText()
    {	
		if( Mage::registry('mastertemplates') && Mage::registry('mastertemplates')->getMasterTempId() ) {
           return Mage::helper('items')->__("Edit Master Template '%s'", $this->htmlEscape(Mage::registry('mastertemplates')->getMasterTempTitle()));
        } else {
            return Mage::helper('items')->__('Master Template');
        }
    }
}