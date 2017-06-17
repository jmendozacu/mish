<?php

class VES_VendorsVacation_Block_Adminhtml_Vacation_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('vacation_form', array('legend'=>Mage::helper('vendorsvacation')->__('Vacation information')));

        $fieldset->addField('message', 'editor', array(
            'name'      => 'message',
            'label'     => Mage::helper('vendorsvacation')->__('Message'),
            'title'     => Mage::helper('vendorsvacation')->__('Message'),
            'style'     => 'width:748px; height:348px;',
            'wysiwyg'   => false,
            'readonly'  => true,
            'disabled'  => true,
            'required'  => true,
            'class'     => 'required-entry',
        ));
        $dateFormatIso = Mage::app()->getLocale() ->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('date_from', 'date', array(
            'label'     => Mage::helper('vendorsvacation')->__('Date From'),
            'title'     => Mage::helper('vendorsvacation')->__('Date From'),
            'name'      => 'date_from',
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'required'  => true,
            'class'     => 'required-entry validate-date',
        ));

        $fieldset->addField('date_to', 'date', array(
            'label'     => Mage::helper('vendorsvacation')->__('Date To'),
            'title'     => Mage::helper('vendorsvacation')->__('Date To'),
            'name'      => 'date_to',
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'required'  => true,
            'class'     => 'required-entry validate-date',
        ));

        $fieldset->addField('product_status', 'select', array(
            'label'     => Mage::helper('vendorsvacation')->__('Disable All Products'),
            'name'      => 'product_status',
            'values'    => VES_VendorsVacation_Model_Source_Product::getOptionArray(),
            'required'  => true,
            'class'     => 'required-entry',
        ));

        $fieldset->addField('vacation_status', 'select', array(
            'label'     => Mage::helper('vendorsvacation')->__('Vacation Status'),
            'name'      => 'vacation_status',
            'values'    => VES_VendorsVacation_Model_Source_Vacation::getOptionArray(),
            'required'  => true,
            'class'     => 'required-entry',
        ));

     
        if(Mage::getSingleton('adminhtml/session')->getVacationData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getVacationData());
            Mage::getSingleton('adminhtml/session')->setVacationData(null);
        }elseif (Mage::registry('vacation_data')) {
            $form->setValues(Mage::registry('vacation_data')->getData());
        }
        return parent::_prepareForm();
    }
}