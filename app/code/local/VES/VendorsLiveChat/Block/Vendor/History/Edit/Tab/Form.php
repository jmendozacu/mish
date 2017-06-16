<?php

class VES_VendorsLiveChat_Block_Vendor_History_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contact_form', array('legend'=>Mage::helper('vendorslivechat')->__('Messages')));

        $history = $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('vendorslivechat')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));
        $history->setRenderer($this->getLayout()->createBlock('vendorslivechat/vendor_history_renderer_view'));
        if ( Mage::getSingleton('adminhtml/session')->getBoxData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBoxData());
            Mage::getSingleton('adminhtml/session')->setBoxData(null);
        } elseif ( Mage::registry('box_data') ) {
            $form->setValues(Mage::registry('box_data')->getData());
        }
        return parent::_prepareForm();
    }
}