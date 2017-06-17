<?php

class VES_Commision_Block_Adminhtml_Managecommision_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('commision_form', array('legend'=>Mage::helper('commision')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('commision')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('commision')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('commision')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('commision')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('commision')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('commision')->__('Content'),
          'title'     => Mage::helper('commision')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCommisionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCommisionData());
          Mage::getSingleton('adminhtml/session')->setCommisionData(null);
      } elseif ( Mage::registry('commision_data') ) {
          $form->setValues(Mage::registry('commision_data')->getData());
      }
      return parent::_prepareForm();
  }
}