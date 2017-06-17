<?php

class Mish_Correoschile_Block_Adminhtml_Correoschile_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('correoschile_form', array('legend'=>Mage::helper('correoschile')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('correoschile')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('correoschile')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('correoschile')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('correoschile')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('correoschile')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('correoschile')->__('Content'),
          'title'     => Mage::helper('correoschile')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCorreoschileData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCorreoschileData());
          Mage::getSingleton('adminhtml/session')->setCorreoschileData(null);
      } elseif ( Mage::registry('correoschile_data') ) {
          $form->setValues(Mage::registry('correoschile_data')->getData());
      }
      return parent::_prepareForm();
  }
}