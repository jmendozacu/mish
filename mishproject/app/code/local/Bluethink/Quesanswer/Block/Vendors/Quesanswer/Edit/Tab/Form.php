<?php

class Bluethink_Quesanswer_Block_Vendors_Quesanswer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('quesanswer_form', array('legend'=>Mage::helper('quesanswer')->__('Question and Answer')));
     
      $fieldset->addField('sku', 'text', array(
          'label'     => Mage::helper('quesanswer')->__('Sku'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'sku',
      ));

      $fieldset->addField('question', 'text', array(
          'label'     => Mage::helper('quesanswer')->__('Question'),
          'required'  => true,
          'name'      => 'question',
	  ));
      $fieldset->addField('answer', 'textarea', array(
          'label'     => Mage::helper('quesanswer')->__('Answer'),
          'required'  => true,
          'name'      => 'answer',
    ));
		
     /* $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('quesanswer')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('quesanswer')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('quesanswer')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('quesanswer')->__('Content'),
          'title'     => Mage::helper('quesanswer')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));*/
     
      if ( Mage::getSingleton('adminhtml/session')->getQuesanswerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getQuesanswerData());
          Mage::getSingleton('adminhtml/session')->setQuesanswerData(null);
      } elseif ( Mage::registry('quesanswer_data') ) {
          $form->setValues(Mage::registry('quesanswer_data')->getData());
      }
      return parent::_prepareForm();
  }
}