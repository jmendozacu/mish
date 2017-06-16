<?php

class VES_VendorsCms_Block_Vendor_Page_Edit_Tab_Design extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design
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
    protected function _prepareForm()
    {
    	parent::_prepareForm();
    	$form = $this->getForm();
    	$form->getElement('design_fieldset')->removeField('custom_theme_from');
    	$form->getElement('design_fieldset')->removeField('custom_theme_to');
    	$form->getElement('design_fieldset')->removeField('custom_theme');
    	$form->getElement('design_fieldset')->removeField('custom_root_template');
    	$form->getElement('design_fieldset')->removeField('custom_layout_update_xml');
    	$form->removeField('design_fieldset');
    }
}