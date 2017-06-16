<?php

class Mercadolibre_Items_Block_Adminhtml_Shippingprofile_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
	  
	  $form = new Varien_Data_Form();
      //$this->setForm($form);
    //  $fieldset = $form->addFieldset('shippingprofile_form', array('legend'=>Mage::helper('items')->__('Add Profile')));
     
     
/*	  $fieldset->addField('shipping_profile', 'text', array(
          'label'     => Mage::helper('items')->__('Shipping Profile'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'shipping_profile',
      ));*/
	  
	  	$CommonModel = Mage::getModel('items/common');
		$optionsShippingType = $CommonModel->getShippingProfileType(); 
		$optionsShippingTypeOption = $CommonModel->getShippingTypeOption(); 
		
/*      $fieldset->addField('shipping_mode', 'select', array(
          'label'     => Mage::helper('items')->__('Shipping Mode'),
          'name'      => 'shipping_mode',
		  'class'     => 'required-entry',
		  'required'  => true,
		  'options'   => $optionsShippingType
      ));*/

/*	  $fieldset->addField('shipping_method', 'select', array(
          'label'     => Mage::helper('items')->__('Shipping Method'),
          'name'      => 'shipping_method',
		  'class'     => 'required-entry',
		  'required'  => true,
         'options'   => $optionsShippingTypeOption
      ));*/
	  
/*	   $fieldset->addField('shipping_service_name[]', 'text', array(
          'label'     => Mage::helper('items')->__('Shipping Service Name'),
          //'class'     => 'required-entry',
         // 'required'  => true,
          'name'      => 'shipping_service_name[]',
      ));*/
	  
/*	   $fieldset->addField('shipping_cost[]', 'text', array(
          'label'     => Mage::helper('items')->__('Shipping Cost'),
          //'class'     => 'required-entry',
         // 'required'  => true,
          'name'      => 'shipping_cost[]',
      ));*/	
	  	  
      if (Mage::getSingleton('adminhtml/session')->getShippingprofilesData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getShippingprofilesData());
          Mage::getSingleton('adminhtml/session')->setgetShippingprofiles(null);
      } elseif ( Mage::registry('shippingprofiles') ) {
          $form->setValues(Mage::registry('shippingprofiles')->getData());
      }
	  
      return parent::_prepareForm();
  }
}