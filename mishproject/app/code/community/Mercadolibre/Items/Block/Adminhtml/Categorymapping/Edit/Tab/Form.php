<?php

class Mercadolibre_Items_Block_Adminhtml_Categorymapping_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('items_form', array('legend'=>Mage::helper('items')->__('Import Category Mapping')));
     
      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('items')->__('Upload File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
     
      if ( Mage::getSingleton('adminhtml/session')->getItemsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getItemsData());
          Mage::getSingleton('adminhtml/session')->setItemsData(null);
      } elseif ( Mage::registry('items_data') ) {
          $form->setValues(Mage::registry('items_data')->getData());
      }
      return parent::_prepareForm();
  }
}