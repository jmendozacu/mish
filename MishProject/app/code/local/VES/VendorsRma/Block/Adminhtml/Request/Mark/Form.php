<?php

class VES_VendorsRma_Block_Adminhtml_Request_Mark_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/saveandsend', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );
      $this->setForm($form);
      $fieldset = $form->addFieldset('customer_form', array('legend'=>Mage::helper('vendorsrma')->__('Message To Customer')));
      
      $collections = Mage::getModel("vendorsrma/mestemplate")->getCollection()
        ->addFieldToFilter("status",VES_VendorsRma_Model_Option_Status::STATUS_ENABLED)
        ->addFieldToFilter("type",VES_VendorsRma_Model_Option_Type::STATUS_CUSTOMER)
      ;
      $status = array(""=>"--  Select Template --");
      foreach($collections as $st){
          $status[$st->getId()] = $st->getTitle();
      }
      
      $fieldset->addField('template', 'select', array(
          'label'     => Mage::helper('vendorsrma')->__('Template'),
          'name'      => 'template',
          'values'    => $status,
          'class'     => 'required-entry',
          'onchange'=> "findTemplateContent(this.value,'content_template')",
          
      ));
      
      $fieldset->addField('content_template', 'hidden', array(
          'label'     => Mage::helper('vendorsrma')->__('Content'),

          'required'  => false,
          'name'      => 'content_template_customer',
          'style'=>"width:700px;height:250px"
      ));
      
      
      $field=$fieldset->addField('submit', 'text', array(
          'value'  => 'Submit',
          'tabindex' => 1
      ));
      $field->setRenderer($this->getLayout()->createBlock('vendorsrma/adminhtml_request_edit_renderer_button'));
       
      $fieldset1 = $form->addFieldset('vendor_form', array('legend'=>Mage::helper('vendorsrma')->__('Message To Vendor')));

      
      $collections = Mage::getModel("vendorsrma/mestemplate")->getCollection()
      ->addFieldToFilter("status",VES_VendorsRma_Model_Option_Status::STATUS_ENABLED)
      ->addFieldToFilter("type",VES_VendorsRma_Model_Option_Type::STATUS_VENDOR)
      ;
      $status = array(""=>"--  Select Template --");
      foreach($collections as $st){
          $status[$st->getId()] = $st->getTitle();
      }
      
      $fieldset1->addField('template1', 'select', array(
          'label'     => Mage::helper('vendorsrma')->__('Template'),
          'name'      => 'template1',
          'values'    => $status,
          'class'     => 'required-entry',
          'onchange'=> "findTemplateContent(this.value,'content_template1')",
      ));
      
      $fieldset1->addField('content_template1', 'hidden', array(
          'label'     => Mage::helper('vendorsrma')->__('Content'),

          'required'  => false,
          'name'      => 'content_template_vendor',
          'style'=>"width:700px;height:250px"
      ));
       
      $field=$fieldset1->addField('submit_1', 'text', array(
          'value'  => 'Submit',
          'tabindex' => 1
      ));
      $field->setRenderer($this->getLayout()->createBlock('vendorsrma/adminhtml_request_edit_renderer_button1'));
       

      $form->setUseContainer(true);
     
      return parent::_prepareForm();
  }
  
 
  
}