<?php
class VES_VendorsRma_Block_Adminhtml_Mestemplate_Edit_Tab_Information extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      
      $fieldset = $form->addFieldset('infomation_form', array('legend'=>Mage::helper('vendorsrma')->__('Template information')));
     
      
      $status = Mage::getModel("vendorsrma/option_status")->getOptions();
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('vendorsrma')->__('Active'),
          'name'      => 'status',
          'values'    => $status,
      ));
      
      
      $type = Mage::getModel("vendorsrma/option_type")->getOptions();
      $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('vendorsrma')->__('Type'),
          'name'      => 'type',
          'values'    => $type,
      ));
      
       $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('vendorsrma')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
       $fieldset->addField('content_template', 'editor', array(
       		'label'     => Mage::helper('vendorsrma')->__('Content'),
       		'name'      => 'content_template',
       		'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
       		'style' => 'width:600px; height:350px;',
       		'wysiwyg' => true,
       		
       ));
      $this->setForm($form);
      if ( Mage::getSingleton('adminhtml/session')->getTemplateData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMestemplateData());
          Mage::getSingleton('adminhtml/session')->setMestemplateData(null);
      } elseif ( Mage::registry('mestemplate_data') ) {
          $form->setValues(Mage::registry('mestemplate_data')->getData());
      }
      return parent::_prepareForm();
  }
}