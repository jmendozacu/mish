<?php

class VES_VendorsCms_Block_Vendor_App_Edit_Tab_Setting extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','app_type')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }
    
	protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('app_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('vendorscms')->__('Settings')));


        $fieldset->addField('type', 'select', array(
            'name'      => 'type',
            'label'     => Mage::helper('vendorscms')->__('Type'),
            'title'     => Mage::helper('vendorscms')->__('Type'),
        	'options'	=> Mage::getSingleton('vendorscms/source_app_type')->getFormOptions(),
            'required'  => true,
        ));
		$fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }
	public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'  => true,
            'type'      => '{{type}}'
        ));
    }
	/**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('vendorscms')->__('Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('vendorscms')->__('Settings');
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