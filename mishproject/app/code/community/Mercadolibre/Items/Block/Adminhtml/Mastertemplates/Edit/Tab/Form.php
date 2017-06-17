<?php

class Mercadolibre_Items_Block_Adminhtml_Mastertemplates_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
	  
	  $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('mastertemplates_form', array('legend'=>Mage::helper('items')->__('Add New Template')));
     
      $fieldset->addField('master_temp_title', 'text', array(
          'label'     => Mage::helper('items')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'master_temp_title',
      ));
	  
		$store_id = Mage::helper('items')->_getStore()->getId();
		$CommonModel = Mage::getModel('items/common');
		$optionsDescTemp = $CommonModel->getDescTemplates($store_id); 
		$optionsShippingTemp = $CommonModel->getShippingTemplates($store_id); 
		$optionsPaymentTemp = $CommonModel->getPaymentTemplates($store_id);
		$optionsListingTemp = $CommonModel->getListingTemplates($store_id); 
		
      $fieldset->addField('profile_id', 'select', array(
          'label'     => Mage::helper('items')->__('Description Template'),
          'name'      => 'profile_id',
		  'class'     => 'required-entry',
		  'required'  => true,
		  'options'   => $optionsDescTemp
      ));
	  
	  $fieldset->addField('shipping_id', 'select', array(
          'label'     => Mage::helper('items')->__('Shipping Template'),
          'name'      => 'shipping_id',
		  'class'     => 'required-entry',
		  'required'  => true,
         'options'   => $optionsShippingTemp
      ));
	  
	  $fieldset->addField('payment_id', 'multiselect', array(
          'label'     => Mage::helper('items')->__('Payment Template'),
          'name'      => 'payment_id',
		  'class'     => 'required-entry',
		   'required'  => true,
		   //'size'  => '4',
          'values'   => $optionsPaymentTemp,
      ));
	  
	 $fieldset->addField('template_id', 'select', array(
          'label'     => Mage::helper('items')->__('Listing Template'),
          'name'      => 'template_id',
		  'class'     => 'required-entry',
		   'required'  => true,
          'options'   => $optionsListingTemp,
      ));
     
     
      if ( Mage::getSingleton('adminhtml/session')->getMastertemplatesData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMastertemplatesData());
          Mage::getSingleton('adminhtml/session')->setMastertemplatesData(null);
      } elseif ( Mage::registry('mastertemplates') ) {
          $form->setValues(Mage::registry('mastertemplates')->getData());
      }

      return parent::_prepareForm();
  }
}