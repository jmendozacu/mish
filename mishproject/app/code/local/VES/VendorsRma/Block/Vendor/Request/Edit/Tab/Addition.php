<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_Addition extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('request_addition', array('legend'=>Mage::helper('vendorsrma')->__('Additional Information')));

        $add=$fieldset->addField('addtiondata', 'text', array(
            'label'     => Mage::helper('vendorsrma')->__('Addition'),
            'name'      => 'addtiondata',
        ));
        $add->setRenderer($this->getLayout()->createBlock('vendorsrma/vendor_request_edit_renderer_addition'));

        return parent::_prepareForm();
    }
}

