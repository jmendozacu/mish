<?php

class Mercadolibre_Items_Block_Adminhtml_Shippingprofile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'items';
        $this->_controller = 'adminhtml_shippingprofile';
        
        $this->_updateButton('save', 'label', Mage::helper('items')->__('Save Template'));
        $this->_updateButton('delete', 'label', Mage::helper('items')->__('Delete Template'));	
    }

    public function getHeaderText()
    {
        if( Mage::registry('shippingprofile') && Mage::registry('shippingprofiles')->getId() ) {
            return Mage::helper('items')->__("Edit Shipping Template '%s'", $this->htmlEscape(Mage::registry('shippingprofiles')->getTitle()));
        } else {
            return Mage::helper('items')->__('Shipping Template');
        }
    }
}