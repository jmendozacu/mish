<?php

class VES_VendorsReview_Block_Adminhtml_Rating_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsreview';
        $this->_controller = 'adminhtml_rating';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorsreview')->__('Save Rating'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorsreview')->__('Delete Rating'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save save-and-continue',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('rating_data') && Mage::registry('rating_data')->getId() ) {
            return Mage::helper('vendorsreview')->__("Edit Rating '%s'", $this->htmlEscape(Mage::registry('rating_data')->getTitle()));
        } else {
            return Mage::helper('vendorsreview')->__('Add Rating');
        }
    }
    
}