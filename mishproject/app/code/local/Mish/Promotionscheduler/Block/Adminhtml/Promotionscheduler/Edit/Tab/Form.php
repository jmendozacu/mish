<?php

class Mish_Promotionscheduler_Block_Adminhtml_Promotionscheduler_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('promotionscheduler_form', array('legend'=>Mage::helper('promotionscheduler')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('promotionscheduler')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('promotionscheduler')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('promotionscheduler')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('promotionscheduler')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('promotionscheduler')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('promotionscheduler')->__('Content'),
          'title'     => Mage::helper('promotionscheduler')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPromotionschedulerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPromotionschedulerData());
          Mage::getSingleton('adminhtml/session')->setPromotionschedulerData(null);
      } elseif ( Mage::registry('promotionscheduler_data') ) {
          $form->setValues(Mage::registry('promotionscheduler_data')->getData());
      }
      return parent::_prepareForm();
  }
}