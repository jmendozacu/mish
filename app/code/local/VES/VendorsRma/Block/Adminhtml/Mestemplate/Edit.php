<?php

class VES_VendorsRma_Block_Adminhtml_Mestemplate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsrma';
        $this->_controller = 'adminhtml_mestemplate';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorsrma')->__('Save template'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorsrma')->__('Delete template'));
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('content_template') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content_template');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content_template');
                }
            }

        
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('mestemplate_data') && Mage::registry('mestemplate_data')->getId() ) {
            return Mage::helper('vendorsrma')->__("Edit Quick Response '%s'", $this->htmlEscape(Mage::registry('mestemplate_data')->getTitle()));
        } else {
            return Mage::helper('vendorsrma')->__('New Quick Response');
        }
    }
    protected function _prepareLayout() {
    	parent::_prepareLayout();
    	if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
    		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    	}
    }
}