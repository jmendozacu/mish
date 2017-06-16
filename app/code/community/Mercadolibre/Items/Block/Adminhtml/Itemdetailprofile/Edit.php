<?php

class Mercadolibre_Items_Block_Adminhtml_Itemdetailprofile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'items';
        $this->_controller = 'adminhtml_itemdetailprofile';
        
        $this->_updateButton('save', 'label', Mage::helper('items')->__('Save Template'));
        $this->_updateButton('delete', 'label', Mage::helper('items')->__('Delete Template'));
      
		$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100); 

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('itemdetailprofile') && Mage::registry('itemdetailprofile')->getId() ) {
            return Mage::helper('items')->__("Edit Description Template '%s'", $this->htmlEscape(Mage::registry('itemdetailprofile')->getTitle()));
        } else {
            return Mage::helper('items')->__('Description Template');
        }
    }
	
}