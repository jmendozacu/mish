<?php

class Mish_Blueexpress_Block_Adminhtml_Blueexpress_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('blueexpress_form', array('legend'=>Mage::helper('blueexpress')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('blueexpress')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('blueexpress')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('blueexpress')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('blueexpress')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('blueexpress')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('blueexpress')->__('Content'),
          'title'     => Mage::helper('blueexpress')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getBlueexpressData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBlueexpressData());
          Mage::getSingleton('adminhtml/session')->setBlueexpressData(null);
      } elseif ( Mage::registry('blueexpress_data') ) {
          $form->setValues(Mage::registry('blueexpress_data')->getData());
      }
      return parent::_prepareForm();
  }
}