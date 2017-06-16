<?php

class OTTO_AdvancedFaq_Block_Adminhtml_Faq_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'advancedfaq';
        $this->_controller = 'adminhtml_faq';
        
        $this->_updateButton('save', 'label', Mage::helper('advancedfaq')->__('Save Faq'));
        $this->_updateButton('delete', 'label', Mage::helper('advancedfaq')->__('Delete Faq'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('kbase_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'kbase_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'kbase_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('faq_data') && Mage::registry('faq_data')->getId() ) {
            return Mage::helper('advancedfaq')->__("Edit Faq '%s'", $this->htmlEscape(Mage::registry('faq_data')->getQuestion()));
        } else {
            return Mage::helper('advancedfaq')->__('Add Faq ');
        }
    }
    protected function _prepareLayout() {
    	$return = parent::_prepareLayout();
    	if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
    		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    	}
    	return $return;
    }
}