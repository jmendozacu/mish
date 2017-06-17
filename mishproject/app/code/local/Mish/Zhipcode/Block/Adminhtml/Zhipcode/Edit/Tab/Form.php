<?php

class Mish_Zhipcode_Block_Adminhtml_Zhipcode_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('zhipcode_form', array('legend'=>Mage::helper('zhipcode')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('zhipcode')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('zhipcode')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('zhipcode')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('zhipcode')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('zhipcode')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('zhipcode')->__('Content'),
          'title'     => Mage::helper('zhipcode')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getZhipcodeData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getZhipcodeData());
          Mage::getSingleton('adminhtml/session')->setZhipcodeData(null);
      } elseif ( Mage::registry('zhipcode_data') ) {
          $form->setValues(Mage::registry('zhipcode_data')->getData());
      }
      return parent::_prepareForm();
  }
}