<?php

class VES_VendorsRma_Block_Adminhtml_Status_Edit_Tab_Tmp extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
       // echo "test";exit;
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('main_tpl', array('legend'=>Mage::helper('vendorsrma')->__('Store Templates')));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field =$fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id[]',
            'label'     => Mage::helper('vendorsrma')->__('Store View'),
            'title'     => Mage::helper('vendorsrma')->__('Store View'),
            'class'     => 'required-entry req',
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
            'name' => 'store_id[]',
            'value' => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('type_data')->setStoreId(Mage::app()->getStore(true)->getId());
        }


        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('vendorsrma')->__('Title'),
            'class'     => 'required-entry req',
            'required'  => true,
            'name'      => 'title[]',
        ));

        $fieldset->addField('template_notify_customer', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to customer (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_customer[]',

        ));


        $fieldset->addField('template_notify_admin', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to administrator (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_admin[]',

        ));


        $fieldset->addField('template_notify_vendor', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to vendor (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_vendor[]',

        ));

        $fieldset->addField('template_notify_history', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notification sent to messages history (leave blank not to send)'),
            'required'  => false,
            'name'      => 'template_notify_history[]',

        ));


        $field=$fieldset->addField('submit', 'text', array(
            'value'  => 'Submit',
            'tabindex' => 1
        ));
        $field->setRenderer($this->getLayout()->createBlock('vendorsrma/adminhtml_status_renderer_button'));

        return parent::_prepareForm();
    }
}