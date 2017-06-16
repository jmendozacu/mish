<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('request_history', array('legend'=>Mage::helper('vendorsrma')->__('Status History')));

        $add=$fieldset->addField('history', 'text', array(
            'label'     => Mage::helper('vendorsrma')->__('Status History'),
            'name'      => 'history',
        ));

        $add->setRenderer($this->getLayout()->createBlock('vendorsrma/adminhtml_request_edit_renderer_history'));


        return parent::_prepareForm();
    }
}

