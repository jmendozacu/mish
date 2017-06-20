<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_Clamber extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('request_addition', array('legend'=>Mage::helper('vendorsrma')->__('Notes')));

        $add=$fieldset->addField('notes', 'text', array(
            'label'     => Mage::helper('vendorsrma')->__('Notes'),
            'name'      => 'notes',
        ));

        if (!Mage::app()->getStore()->isAdmin()) {
            $add->setRenderer($this->getLayout()->createBlock('vendorsrma/vendor_request_edit_renderer_clamber'));
        }
        else{
            $add->setRenderer($this->getLayout()->createBlock('vendorsrma/adminhtml_request_edit_renderer_clamber'));
        }

        return parent::_prepareForm();
    }
}

