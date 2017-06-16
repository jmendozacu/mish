<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_Address extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contact', array('legend'=>Mage::helper('vendorsrma')->__('Contact Information')));

        $fieldset->addField('firstname',  Mage::app()->getStore()->isAdmin() ? 'text' : 'label', array(
            'label'     => Mage::helper('vendorsrma')->__('First Name '),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'address[firstname]',
        ));

        $fieldset->addField('lastname', Mage::app()->getStore()->isAdmin() ? 'text' : 'label', array(
            'label'     => Mage::helper('vendorsrma')->__('Last Name '),
            'class'     => 'required-entry',
            'name'      => 'address[lastname]',
            'required'  => true,
        ));

        $fieldset->addField('company',  Mage::app()->getStore()->isAdmin() ? 'text' : 'label', array(
            'label'     => Mage::helper('vendorsrma')->__('Company'),
            'name'      => 'address[company]',
        ));

        $fieldset->addField('telephone',  Mage::app()->getStore()->isAdmin() ? 'text' : 'label', array(
            'label'     => Mage::helper('vendorsrma')->__('Telephone'),
            'class'     => 'required-entry',
            'name'      => 'address[telephone]',
            'required'  => true,
        ));

        $fieldset->addField('fax',  Mage::app()->getStore()->isAdmin() ? 'text' : 'label' , array(
            'label'     => Mage::helper('vendorsrma')->__('Fax'),
            'name'      => 'address[fax]',
        ));


        $fieldset_1 = $form->addFieldset('return_address', array('legend'=>Mage::helper('vendorsrma')->__('Return Address')));


        $fieldset_1->addField('address',  Mage::app()->getStore()->isAdmin() ? 'text' : 'label' , array(
            'label'     => Mage::helper('vendorsrma')->__('Street Address'),
            'class'     => 'required-entry',
            'name'      => 'address[address]',
            'required'  => true,
        ));

        $fieldset_1->addField('city',  Mage::app()->getStore()->isAdmin() ? 'text' : 'label' , array(
            'label'     => Mage::helper('vendorsrma')->__('City'),
            'class'     => 'required-entry',
            'name'      => 'address[city]',
            'required'  => true,
        ));

        $countryOptions = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
        $cOptions = array();
        foreach($countryOptions as $option){
            $cOptions[$option['value']] = $option['label'];
        }
        $fieldset_1->addField('country_id',  Mage::app()->getStore()->isAdmin() ? 'select' : 'label', array(
            'label'     => Mage::helper('vendorsrma')->__('Country'),
            'name'      => 'address[country_id]',
            'class'     => 'required-entry',
            'options'	  => $cOptions,
            'required'  => true,
        ));
        $fieldset_1->addField('region', Mage::app()->getStore()->isAdmin() ? 'text' : 'label' , array(
            'label'     => Mage::helper('vendorsrma')->__('State/Province'),
            'required'  => true,
            'class'     => 'required-entry',
            'name'      => 'address[region]',
        ));

        $fieldset_1->addField('region_id',  Mage::app()->getStore()->isAdmin() ? 'select' : 'label', array(
            'label'     => Mage::helper('vendorsrma')->__('State/Province'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'address[region_id]',
        ));

        $fieldset_1->addField('postcode', Mage::app()->getStore()->isAdmin() ? 'text' : 'label' , array(
            'label'     => Mage::helper('vendorsrma')->__('Zip/Postal Code'),
            'name'      => 'address[postcode]',
        ));


        $fieldset_2 = $form->addFieldset('additions', array('legend'=>Mage::helper('vendorsrma')->__('Additional Information')));

        $fieldset_2->addField('additional_information', 'textarea', array(
            'label'     => Mage::helper('vendorsrma')->__(''),
            'name'      => 'address[additional_information]',
        ));

        if(Mage::app()->getStore()->isAdmin() ){
            $regionElement = $form->getElement('region');
            $regionElement->setRequired(true);
            if ($regionElement) {
                $regionElement->setRenderer(Mage::getModel('vendorsrma/system_config_renderer_region'));
            }
         }
        $regionElement = $form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }


        if ( Mage::registry('current_addresss') ) {
            $form->setValues(Mage::registry('current_addresss')->getData());
        }
        return parent::_prepareForm();
    }
}