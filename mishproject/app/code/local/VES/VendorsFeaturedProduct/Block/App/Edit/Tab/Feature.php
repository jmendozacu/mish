<?php
class VES_VendorsFeaturedProduct_Block_App_Edit_Tab_Feature extends VES_VendorsCms_Block_Vendor_App_Edit_Tab_Option{
	
	protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('cms_app');


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('app_');

        $fieldset = $form->addFieldset('frontend_app_option_fieldset', array('legend'=>Mage::helper('vendorscms')->__('Frontend App Options')));
    	if ($model->getAppId()) {
            $fieldset->addField('featureproduct_option_option_id', 'hidden', array(
                'name' => 'featureproduct_option[option_id]',
            ));
        }
        $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
        
        $fieldset->addField('featureproduct_option_template', 'select', array(
            'name'      => 'featureproduct_option[template]',
            'label'     => Mage::helper('vendorsfeaturedproduct')->__('Template'),
            'title'     => Mage::helper('vendorsfeaturedproduct')->__('Template'),
        	'options'	=> Mage::getModel('vendorsfeaturedproduct/source_vendor_template')->getOptionArray($vendorId,true),
            'required'  => true,
        ));
		
        $fieldset->addField('featureproduct_option_colunms_num', 'text', array(
        		'name'      => 'featureproduct_option[colunms_num]',
        		'label'     => Mage::helper('vendorsfeaturedproduct')->__('Products Per Row'),
        		'title'     => Mage::helper('vendorsfeaturedproduct')->__('Products Per Row'),
        		'required'  => true,
        		'class'=>"validate-number"
        ));
        
        $fieldset->addField('featureproduct_option_sort_by', 'select', array(
        		'name'      => 'featureproduct_option[sort_by]',
        		'label'     => Mage::helper('vendorsfeaturedproduct')->__('Product listing sort by'),
        		'title'     => Mage::helper('vendorsfeaturedproduct')->__('Product listing sort by'),
        		'options'	=> Mage::getModel('vendorsfeaturedproduct/source_vendor_listSort')->getOptionArray(),
        		'required'  => true,
        ));
        $fieldset->addField('featureproduct_option_direct', 'select', array(
        		'name'      => 'featureproduct_option[direct]',
        		'label'     => Mage::helper('vendorsfeaturedproduct')->__('Direction'),
        		'title'     => Mage::helper('vendorsfeaturedproduct')->__('Direction'),
        		'options'	=> Mage::getModel('vendorsfeaturedproduct/direction')->toOptionArray(),
        		'required'  => true,
        ));
        $fieldset->addField('featureproduct_option_cache_time', 'text', array(
        		'name'      => 'featureproduct_option[cache_time]',
        		'label'     => Mage::helper('vendorsfeaturedproduct')->__('Cache Lifetime (seconds)'),
        		'title'     => Mage::helper('vendorsfeaturedproduct')->__('Cache Lifetime (seconds)'),
        		'note'	=> $this->__('24 hours by default'),
        		'required'  => true,
        		'class'=>"validate-number"
        ));
        
      
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}