<?php

class VES_VendorsCms_Block_Vendor_Page_Edit_Tab_Main extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Main
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
    	/*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
    	
    	$baseFieldset = $this->getForm()->getElement('base_fieldset');
    	$baseFieldset->removeField('store_id');
    	$baseFieldset->addField('root_template', 'select', array(
            'name'     => 'root_template',
            'label'    => Mage::helper('cms')->__('Layout'),
            'required' => true,
            'values'   => Mage::getSingleton('page/source_layout')->toOptionArray(),
            'disabled' => $isElementDisabled
        ));
        $model = Mage::registry('cms_page');
        if (!$model->getId()) {
            $this->getForm()->getElement('root_template')->setValue('one_column');
        }else{
        	$this->getForm()->getElement('root_template')->setValue($model->getData('root_template'));
        }
    }
}