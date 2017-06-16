<?php

class Mercadolibre_Items_Block_Adminhtml_Itemtemplates_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
	  
	  $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('itemtemplates_form', array('legend'=>Mage::helper('items')->__('Listing Template')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('items')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
	  
	  	$CommonModel = Mage::getModel('items/common');
		$optionsBuyingType = $CommonModel->getBuyingType(); 
		$optionsListingType = $CommonModel->getListingType(); 
		$optionsCondition = $CommonModel->getCondition(); 
		
      $fieldset->addField('buying_mode_id', 'select', array(
          'label'     => Mage::helper('items')->__('Buying Type'),
          'name'      => 'buying_mode_id',
		  'class'     => 'required-entry',
		  'required'  => true,
		  'options'   => $optionsBuyingType
      ));
	  
	  $fieldset->addField('listing_type_id', 'select', array(
          'label'     => Mage::helper('items')->__('Listing Type'),
          'name'      => 'listing_type_id',
		  'class'     => 'required-entry',
		  'required'  => true,
         'options'   => $optionsListingType
      ));
	  
	  $fieldset->addField('condition_id', 'select', array(
          'label'     => Mage::helper('items')->__('Condition'),
          'name'      => 'condition_id',
		  'class'     => 'required-entry',
		   'required'  => true,
          'options'   => $optionsCondition,
      ));
     
     
      if ( Mage::getSingleton('adminhtml/session')->getItemtemplatesData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getItemtemplatesData());
          Mage::getSingleton('adminhtml/session')->setItemtemplatesData(null);
      } elseif ( Mage::registry('itemtemplates') ) {
          $form->setValues(Mage::registry('itemtemplates')->getData());
      }

      return parent::_prepareForm();
  }
}