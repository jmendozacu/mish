<?php

class Bluethink_Btslider_Block_Adminhtml_Btslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('btslider_form', array('legend'=>Mage::helper('btslider')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('btslider')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('btslider')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('btslider')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('btslider')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('btslider')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('btslider')->__('Content'),
          'title'     => Mage::helper('btslider')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getBtsliderData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBtsliderData());
          Mage::getSingleton('adminhtml/session')->setBtsliderData(null);
      } elseif ( Mage::registry('btslider_data') ) {
          $form->setValues(Mage::registry('btslider_data')->getData());
      }
      return parent::_prepareForm();
  }
}