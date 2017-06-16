<?php

class VES_VendorsCms_Block_Vendor_Block_Edit_Form extends Mage_Adminhtml_Block_Cms_Block_Edit_Form{
	protected function _prepareForm(){
		parent::_prepareForm();
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        
        $wysiwygConfig['add_widgets'] = false;
        $wysiwygConfig['add_variables'] = false;
        $this->getForm()->getElement('content')->setData('config',$wysiwygConfig);
        $this->getForm()->getElement('content')->setData('note',Mage::helper('vendorscms')->getFilterNotes());
        $this->getForm()->setAction($this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('block_id'))));
        $this->getForm()->getElement('base_fieldset')->removeField('store_id');
        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
	}
}