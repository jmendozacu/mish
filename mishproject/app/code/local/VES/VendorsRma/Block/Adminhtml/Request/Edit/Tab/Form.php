<?php

class VES_VendorsRma_Block_Adminhtml_Reason_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('reason_form', array('legend'=>Mage::helper('vendorsrma')->__('Reason information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('vendorsrma')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      /**
       * Check is single store mode
       */
      if (!Mage::app()->isSingleStoreMode()) {
          $field =$fieldset->addField('store_id', 'multiselect', array(
              'name'      => 'store_id[]',
              'label'     => Mage::helper('vendorsrma')->__('Store View'),
              'title'     => Mage::helper('vendorsrma')->__('Store View'),
              'required'  => true,
              'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
          ));
      }
      else {
          $fieldset->addField('store_id', 'hidden', array(
              'name' => 'store_id[]',
              'value' => Mage::app()->getStore(true)->getId()
          ));
          Mage::registry('type_data')->setStoreId(Mage::app()->getStore(true)->getId());
      }

      $fieldset->addField('sort_order', 'text', array(
          'label'     => Mage::helper('vendorsrma')->__('Sort Order'),
          'required'  => true,
          'class'=> "required-entry validate-number",
          'name'      => 'sort_order',
      ));

      $status = Mage::getModel("vendorsrma/option_status")->getOptions();
      $fieldset->addField('active', 'select', array(
          'label'     => Mage::helper('vendorsrma')->__('Status'),
          'name'      => 'active',
          'values'    => $status,
      ));

     
      if ( Mage::getSingleton('adminhtml/session')->getReasonData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getReasonData());
          Mage::getSingleton('adminhtml/session')->setReasonData(null);
      } elseif ( Mage::registry('reason_data') ) {
          $form->setValues(Mage::registry('reason_data')->getData());
      }
      return parent::_prepareForm();
  }
}