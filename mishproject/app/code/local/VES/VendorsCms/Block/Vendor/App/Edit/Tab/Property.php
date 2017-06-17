<?php

class VES_VendorsCms_Block_Vendor_App_Edit_Tab_Property extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
   
	protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('cms_app');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('app_');

        $fieldset = $form->addFieldset('property_fieldset', array('legend'=>Mage::helper('vendorscms')->__('Frontend Properties')));

        if ($model->getAppId()) {
            $fieldset->addField('app_id', 'hidden', array(
                'name' => 'app_id',
            ));
        }
		$fieldset->addField('type', 'select', array(
            'name'      => 'type',
            'label'     => Mage::helper('vendorscms')->__('Type'),
            'title'     => Mage::helper('vendorscms')->__('Type'),
        	'options'	=> Mage::getSingleton('vendorscms/source_app_type')->getFormOptions(),
			'disabled'	=> true,
            'required'  => true,
        ));
        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('vendorscms')->__('Frontend App Instance Title'),
            'title'     => Mage::helper('vendorscms')->__('Frontend App Instance Title'),
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => Mage::helper('vendorscms')->__('Sort Order'),
            'title'     => Mage::helper('vendorscms')->__('Sort Order'),
            'required'  => false,
            'class'     => 'validate-number',
            'note'      => Mage::helper('vendorscms')->__('Sort Order of frontend apps instance in the same block reference'),
            'disabled'  => $isElementDisabled
        ));
        
		$fieldset1 = $form->addFieldset('layout_fieldset', array('class'=>'fieldset-wide','legend'=>Mage::helper('vendorscms')->__('Layout Updates')));
        $fieldset1->addType('layout_update','VES_VendorsCms_Block_Form_Element_Layout');
        
        $fieldset1->addField('layout_update', 'layout_update', array(
            'name'      => 'layout_update',
        ));
        
		$headerBar = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Layout Update'),
                    'onclick'   => "frontendInstance.addLayoutUpdate();",
                    'class'     => 'save'
                    ));
		$fieldset1->setHeaderBar($headerBar->toHtml());
		
        $form->setValues($model->getData());
        if(!$model->getId()){
        	$form->getElement('type')->setValue($this->getRequest()->getParam('type'));
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
	/**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('vendorscms')->__('Frontend Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('vendorscms')->__('Frontend Properties');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

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
}