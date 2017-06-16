<?php

class Mercadolibre_Items_Block_Adminhtml_Questions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
    $fieldset = $form->addFieldset('items_form', array('legend'=>Mage::helper('items')->__('Question information')));

	$ansTemplateArr = array(''=>'Please Select','new_template'=>'New Template');
	$ansTemplateCollection = Mage::getModel('items/melianswertemplate')->getCollection();
	if(count($ansTemplateCollection->getData()) > 0 ){
		foreach($ansTemplateCollection->getData() as $row){
			$ansTemplateArr[$row['answer_template_id']] = $row['title'];
		}
	}

	  $fieldset->addField('answer_template_id', 'select', array(
          'label'     => Mage::helper('items')->__('Answer Template'),
          'name'      => 'answer_template_id',
		  'onchange'  => 'getAnswerTemplate(this.value);',
          'values'    => $ansTemplateArr
      ));
	  $fieldset->addField('title', 'text', array(
		  'label'     => Mage::helper('items')->__('Answer Template Title'),
		  //'class'     => 'required-entry',
		  'required'  => false,
		  'name'      => 'title',
	  ));
	  $fieldset->addField('question_id', 'hidden', array(
          'label'     => Mage::helper('items')->__('ID'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'question_id',
      )); 
	  $fieldset->addField('question', 'textarea', array(
          'label'     => Mage::helper('items')->__('Question'),
          //'class'     => 'required-entry',
          //'required'  => true,
		  'style' => 'resize: none; width:600px; height:100px;',
          'name'      => 'question',
      ));  
  	 $fieldset->addField('id', 'hidden', array(
          'label'     => Mage::helper('items')->__('ID'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'id',
      ));  
     
      $fieldset->addField('answer', 'editor', array(
          'name'      => 'answer',
          'label'     => Mage::helper('items')->__('Answer'),
          'title'     => Mage::helper('items')->__('Content'),
          'style'     => 'resize: none; width:600px; height:200px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getItemsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getItemsData());
          Mage::getSingleton('adminhtml/session')->setItemsData(null);
      } elseif ( Mage::registry('questions') ) {
          $form->setValues(Mage::registry('questions')->getData());
      }
      return parent::_prepareForm();
  }
}