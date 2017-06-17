<?php

class VES_VendorsCategory_Block_App_Edit_Tab_Category extends VES_VendorsCms_Block_Vendor_App_Edit_Tab_Option{
   	
	protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('cms_app');


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('app_');

        $fieldset = $form->addFieldset('frontend_app_option_fieldset', array('legend'=>Mage::helper('vendorscms')->__('Frontend App Options')));
    	if ($model->getAppId()) {
            $fieldset->addField('category_option_option_id', 'hidden', array(
                'name' => 'category_option[option_id]',
            ));
        }
        $fieldset->addField('category_option_template', 'select', array(
            'name'      => 'category_option[template]',
            'label'     => Mage::helper('vendorscategory')->__('Navigation Type'),
            'title'     => Mage::helper('vendorscategory')->__('Navigation Type'),
        	'options'	=> $this->getNavigationTemplates(),
            'required'  => true,
        ));
		
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Get all navigation templates
     * @throws Mage_Core_Exception
     */
    public function getNavigationTemplates(){
    	$config = Mage::getConfig()->getNode('vendors/navigation_templates')->asArray();
    	$options = array();
    	$options[''] = Mage::helper('vendorscategory')->__('-- Please Select --'); 
    	foreach($config as $key => $type){
    		/*Get helper*/
    		if(isset($type['@']) && isset($type['@']['module'])){
    			$helper = Mage::helper($type['@']['module']);
    		}else{
    			$helper = Mage::helper('core');
    		}
    		if(!isset($type['template'])) throw new Mage_Core_Exception(Mage::helper('vendorscategory')->__('Navigation template is not defined on "%s" page group',$type['title']));
    		//$type['title'] = $helper->__($type['title']);
    		$options[$type['template']] = $helper->__($type['title']);
    	}
    	return $options;
    }
}