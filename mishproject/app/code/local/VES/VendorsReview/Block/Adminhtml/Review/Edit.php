<?php

class VES_VendorsReview_Block_Adminhtml_Review_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsreview';
        $this->_controller = 'adminhtml_review';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorsreview')->__('Save Review'));
        //$this->_updateButton('delete', 'label', Mage::helper('vendorsreview')->__('Delete Review'));
        $this->_removeButton('delete');
        
        if(Mage::registry('useAdminMode')) {
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
        else {
        	$this->_removeButton('save');
        	$this->removeButton('reset');
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('review_data') && Mage::registry('review_data')->getId() ) {
        	if(Mage::registry('useAdminMode')) {
            	return Mage::helper('vendorsreview')->__("Edit Review #%s", $this->htmlEscape(Mage::registry('review_data')->getId()));
        	}else {
        		return Mage::helper('vendorsreview')->__("Review #%s", $this->htmlEscape(Mage::registry('review_data')->getId())); 
        	}
        } else {
            return Mage::helper('vendorsreview')->__('Add Review');
        }
    }
    
}