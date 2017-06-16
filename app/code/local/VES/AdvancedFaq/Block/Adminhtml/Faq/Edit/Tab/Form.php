<?php

class OTTO_AdvancedFaq_Block_Adminhtml_Faq_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('kbase_form', array('legend'=>Mage::helper('advancedfaq')->__('Faq information'),'class'=>'fieldset-wide'));
     
      $fieldset->addField('question', 'text', array(
          'label'     => Mage::helper('advancedfaq')->__('Question'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'question',
      ));
      $fieldset->addField('status', 'select', array(
      		'label'     => Mage::helper('advancedfaq')->__('Status'),
      		'name'      => 'status',
      		'values'    => OTTO_AdvancedFaq_Model_Status::getStatusOption(),
      ));
      $fieldset->addField('category_id', 'select', array(
      		'label'     => Mage::helper('advancedfaq')->__('Add in topic'),
      		'class'     => 'required-entry',
      		'required'  => true,
      		'name'      => 'category_id',
      		'values'    => Mage::getModel('advancedfaq/faq')->getCategoryOptions(),
      ));
      $fieldset->addField('sort_order', 'text', array(
      		'label'     => Mage::helper('advancedfaq')->__('Order/Position'),
      		'class'     => 'required-entry',
      		'name'      => 'sort_order',
      		'style'=>"width:200px;"
      ));
      $fieldset->addField('show_on', 'select', array(
      		'label'     => Mage::helper('advancedfaq')->__('Show on main page'),
      		'name'      => 'show_on',
      		'values'    => OTTO_AdvancedFaq_Model_Status::getStatusShowArray(),
      ));
      $fieldset->addField('answer', 'editor', array(
          'name'      => 'answer',
          'label'     => Mage::helper('advancedfaq')->__('Answer'),
          'title'     => Mage::helper('advancedfaq')->__('Answer'),
          'style'     => 'height:250px;',
          'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
          'required'  => true,
      ));
	
      /*
      $fieldset->addField('author', 'text', array(
      		'label'     => Mage::helper('advancedfaq')->__('Author'),
      		'required'  => false,
      		'name'      => 'author',
      ));
     
      if(!Mage::registry('faq_data')->getAuthor()){
      		Mage::registry('faq_data')->setAuthor(Mage::getSingleton('admin/session')->getUser()->getName());
      }
       
      $fieldset->addField('tags', 'text', array(
      		'label'     => Mage::helper('advancedfaq')->__('Tags'),
      		'required'  => false,
      		'name'      => 'tags',
      		'note'		=> Mage::helper('advancedfaq')->__('Seperate tags with commas'),
      		'onchange'	=> 'this.value = this.value.replace( /\s\s+/g, \' \' ).replace(/(.*?)\s*,\s*(.*?)/g,\'$1,$2\')',
      ));
      
      $fieldset->addField('url_key', 'text', array(
      		'label'     => Mage::helper('advancedfaq')->__('Url Key'),
      		'class'     => 'validate-xml-identifier',
      		'required'  => true,
      		'name'      => 'url_key',
      ));

  		/**
         * Check is single store mode
      
        if (!Mage::app()->isSingleStoreMode()) {
            $field =$fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'store_id[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('faq_data')->setStoreId(Mage::app()->getStore(true)->getId());
        }
           */
    
      
    
     
     
      if ( Mage::getSingleton('adminhtml/session')->getFaqData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFaqData());
          Mage::getSingleton('adminhtml/session')->setFaqData(null);
      } elseif ( Mage::registry('faq_data') ) {
          $form->setValues(Mage::registry('faq_data')->getData());
      }
      return parent::_prepareForm();
  }
}