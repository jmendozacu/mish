<?php

class Mish_Personallogistic_Block_Adminhtml_Personallogisticorders_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('personallogistic_form', array('legend'=>Mage::helper('personallogistic')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('personallogistic')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('personallogistic')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('personallogistic')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('personallogistic')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('personallogistic')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('personallogistic')->__('Content'),
          'title'     => Mage::helper('personallogistic')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPersonallogisticData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPersonallogisticData());
          Mage::getSingleton('adminhtml/session')->setPersonallogisticData(null);
      } elseif ( Mage::registry('personallogistic_data') ) {
          $form->setValues(Mage::registry('personallogistic_data')->getData());
      }
      return parent::_prepareForm();
  }
}