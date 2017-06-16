<?php

class VES_VendorsCms_Block_Vendor_Page_Edit_Tab_Content extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Content
{
	/**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return true;
    }
    
    protected function _prepareForm(){
    	parent::_prepareForm();
    	$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );
        $wysiwygConfig['add_widgets'] = false;
        $wysiwygConfig['add_variables'] = false;
        $contentField = $this->getForm()->getElement('content');
        $contentField->setData('config',$wysiwygConfig);
        $contentField->getRenderer()->setTemplate('ves_vendorscms/page/edit/form/renderer/content.phtml');
       	$contentField->setData('note',Mage::helper('vendorscms')->getFilterNotes());
    }
}