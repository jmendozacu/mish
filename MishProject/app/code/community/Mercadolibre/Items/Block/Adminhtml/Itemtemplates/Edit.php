<?php

class Mercadolibre_Items_Block_Adminhtml_Itemtemplates_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'items';
        $this->_controller = 'adminhtml_itemtemplates';
        
        $this->_updateButton('save', 'label', Mage::helper('items')->__('Save Template'));
        $this->_updateButton('delete', 'label', Mage::helper('items')->__('Delete Template'));
    }

    public function getHeaderText()
    {	
		if( Mage::registry('itemtemplates') && Mage::registry('itemtemplates')->getId() ) {
            return Mage::helper('items')->__("Edit Listing Template '%s'", $this->htmlEscape(Mage::registry('itemtemplates')->getTitle()));
        } else {
            return Mage::helper('items')->__('Listing Template');
        }
    }
}