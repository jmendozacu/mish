<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_Note extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('notes', array('legend'=>Mage::helper('vendorsrma')->__('Notes')));

        $fieldset->addField('note', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__('Notes'),
            'name'      => 'request[note]',
        ));

        if ( Mage::registry('current_request') ) {
            $form->setValues(Mage::registry('current_request')->getData());
        }
        return parent::_prepareForm();
    }
}