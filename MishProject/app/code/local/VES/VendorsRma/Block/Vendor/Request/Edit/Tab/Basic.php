<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_Basic extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('basic_form', array('legend'=>Mage::helper('vendorsrma')->__('Request Details')));

        $order =  $fieldset->addField('order_incremental_id', 'text', array(
            'label'     => Mage::helper('vendorsrma')->__('Order ID'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'order_incremental_id',
        ));

        $order->setRenderer($this->getLayout()->createBlock('vendorsrma/vendor_request_edit_renderer_order'));

        $pack = Mage::getModel("vendorsrma/option_pack")->getOptions();
        $fieldset->addField('package_opened', 'select', array(
            'label'     => Mage::helper('vendorsrma')->__('Package Opened'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'package_opened',
            'values'    => $pack,
        ));

        return parent::_prepareForm();
    }
}