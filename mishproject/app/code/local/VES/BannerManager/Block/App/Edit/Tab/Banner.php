<?php

class VES_BannerManager_Block_App_Edit_Tab_Banner extends VES_VendorsCms_Block_Vendor_App_Edit_Tab_Option{
   	
	protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('cms_app');


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('app_');

        $fieldset = $form->addFieldset('frontend_app_option_fieldset', array('legend'=>Mage::helper('vendorscms')->__('Frontend App Options')));
    	if ($model->getAppId()) {
            $fieldset->addField('banner_option_option_id', 'hidden', array(
                'name' => 'banner_option[option_id]',
            ));
        }
        $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
        $fieldset->addField('banner_option_banner_id', 'select', array(
            'name'      => 'banner_option[banner_id]',
            'label'     => Mage::helper('bannermanager')->__('Banner'),
            'title'     => Mage::helper('bannermanager')->__('Banner'),
        	'options'	=> Mage::getModel('bannermanager/source_vendor_banner')->getOptionArray($vendorId,true),
            'required'  => true,
        ));
		
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}