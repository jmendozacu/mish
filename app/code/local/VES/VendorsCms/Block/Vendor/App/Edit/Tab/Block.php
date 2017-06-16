<?php

class VES_VendorsCms_Block_Vendor_App_Edit_Tab_Block extends VES_VendorsCms_Block_Vendor_App_Edit_Tab_Option{
   
	protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('cms_app');


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('app_');

        $fieldset = $form->addFieldset('frontend_app_option_fieldset', array('legend'=>Mage::helper('vendorscms')->__('Frontend App Options')));
    	if ($model->getAppId()) {
            $fieldset->addField('block_app_option_option_id', 'hidden', array(
                'name' => 'block_app_option[option_id]',
            ));
        }
        $fieldset->addField('block_app_option_block_id', 'select', array(
            'name'      => 'block_app_option[block_id]',
            'label'     => Mage::helper('vendorscms')->__('Block'),
            'title'     => Mage::helper('vendorscms')->__('Block'),
        	'options'	=> Mage::getModel('vendorscms/source_block')->getOptionArray(),
            'required'  => true,
        ));
		
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}