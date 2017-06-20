<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Block_Adminhtml_Saveconfig_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $configsfolder;

    public function __construct()
    {
        parent::__construct();
        $this->configsfolder='ev_skins';
    }

    protected function _prepareForm()
    {
        $form_builder = new Varien_Data_Form(array(
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form_builder->addFieldset('action_fieldset', array('legend'=>Mage::helper('welldone')->__('Save your own preset config from current configuration of Welldone')));

        $fieldset->addField('store_id', 'select', array(
            'name'      => 'store',
            'title'     => Mage::helper('cms')->__('Store View'),
            'label'     => Mage::helper('cms')->__('Store View'),
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ));


        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'title'     => Mage::helper('cms')->__('Name of the new preset config'),
            'label'     => Mage::helper('cms')->__('Name of the new preset config'),
            'class' => 'required-entry validate-alpha',
        ));


        $form_builder->setMethod('post');
        $form_builder->setAction($this->getUrl('*/*/saveconfig'));
        $form_builder->setUseContainer(true);
        $form_builder->setId('edit_form');
        $this->setForm($form_builder);
        return parent::_prepareForm();
    }



}
