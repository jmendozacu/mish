<?php

class Mish_Shipit_Block_Adminhtml_Shipit_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('shipit_form', array('legend'=>Mage::helper('shipit')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('shipit')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('shipit')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('shipit')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('shipit')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('shipit')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('shipit')->__('Content'),
          'title'     => Mage::helper('shipit')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getShipitData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getShipitData());
          Mage::getSingleton('adminhtml/session')->setShipitData(null);
      } elseif ( Mage::registry('shipit_data') ) {
          $form->setValues(Mage::registry('shipit_data')->getData());
      }
      return parent::_prepareForm();
  }
}