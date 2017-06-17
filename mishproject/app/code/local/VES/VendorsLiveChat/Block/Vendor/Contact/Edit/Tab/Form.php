<?php

class VES_VendorsLiveChat_Block_Vendor_Contact_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contact_form', array('legend'=>Mage::helper('vendorslivechat')->__('Contact information')));

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('vendorslivechat')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('vendorslivechat')->__('Email'),
            'required'  => true,
            "class"=>"validate-email",
            'name'      => 'email',
        ));
        $fieldset->addField('question', 'text', array(
            'name'      => 'question',
            'label'     => Mage::helper('vendorslivechat')->__('Question'),
            'title'     => Mage::helper('vendorslivechat')->__('Question'),
            'required'  => true,
        ));
        $fieldset->addField('note', 'editor', array(
            'name'      => 'note',
            'label'     => Mage::helper('vendorslivechat')->__('Note'),
            'title'     => Mage::helper('vendorslivechat')->__('Note'),
            'style'     => 'width:400px; height:200px;',
            'wysiwyg'   => false,
            'required'  => false,
        ));

        if ( Mage::getSingleton('adminhtml/session')->getContactData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContactData());
            Mage::getSingleton('adminhtml/session')->setContactData(null);
        } elseif ( Mage::registry('contact_data') ) {
            $form->setValues(Mage::registry('contact_data')->getData());
        }
        return parent::_prepareForm();
    }
}