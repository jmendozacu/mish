<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('details', array('legend'=>Mage::helper('vendorsrma')->__('Request Details')));
     
      $oder = $fieldset->addField('order_incremental_id', 'text', array(
          'label'     => Mage::helper('vendorsrma')->__('Order Id'),
          'class'     => 'order_incremental_id',
          'name'      => 'title',
      ));

      $oder->setRenderer($this->getLayout()->createBlock('vendorsrma/vendor_request_edit_renderer_linkorder'));

      $customer = $fieldset->addField('customer_name', 'text', array(
          'label'     => Mage::helper('vendorsrma')->__('Customer Name'),
          'required'  => true,
          'name'      => 'customer_name',
      ));

      $customer->setRenderer($this->getLayout()->createBlock('vendorsrma/vendor_request_edit_renderer_customer'));

      $fieldset_1 = $form->addFieldset('options', array('legend'=>Mage::helper('vendorsrma')->__('Request Options')));

      $pack = Mage::getModel("vendorsrma/option_pack")->getOptions();

      $fieldset_1->addField('package_opened', 'select', array(
          'label'     => Mage::helper('vendorsrma')->__('Package Opened'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'request[package_opened]',
          'values'    => $pack,
      ));


      $status = Mage::getModel("vendorsrma/status")->getOptions();
      $fieldset_1->addField('status', 'select', array(
          'label'     => Mage::helper('vendorsrma')->__('Set status to'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'request[status]',
          'values'    => $status,
      ));


      $type = Mage::getModel("vendorsrma/type")->getOptions();
      $fieldset_1->addField('type', 'select', array(
          'label'     => Mage::helper('vendorsrma')->__('Request type'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'request[type]',
          'values'    => $type,
      ));

      $fieldset_1->addField('tracking_code', 'text', array(
          'label'     => Mage::helper('vendorsrma')->__('Post tracking code'),
          'name'      => 'request[tracking_code]',
      ));


      if(Mage::helper('vendorsrma/config')->allowReasons()):
          $fieldset_2 = $form->addFieldset('reasons', array('legend'=>Mage::helper('vendorsrma')->__('Reason Details')));

          if(Mage::helper('vendorsrma/config')->enableReasons()):

          $fisrt =  array("value"=>"","label"=>"--- Select Reason ---");
          $reason = Mage::getModel("vendorsrma/reason")->getOptions();
          array_unshift($reason,$fisrt);
          $fieldset_2->addField('reason', 'select', array(
              'label'     => Mage::helper('vendorsrma')->__('Reason'),
              'class'     => 'required-entry',
              'required'  => true,
              'name'      => 'request[reason]',
              'values'    => $reason,
          ));
          endif;

          if(Mage::helper('vendorsrma/config')->allowOtherOptionReason()):
              $fieldset_2->addField('reason_detail', 'text', array(
                  'label'     => Mage::helper('vendorsrma')->__('Other Reason'),
                  'name'      => 'request[reason_detail]',
              ));
          endif;

      endif;



      $fieldset_3 = $form->addFieldset('comments', array('legend'=>Mage::helper('vendorsrma')->__('Add Comment')));

     // $wysiwygConfig = Mage::getModel('vendorsrma/cms_wysiwyg_config')->getConfigSytem();
      $fieldset_3->addField('comment', 'editor', array(
          'label'     => Mage::helper('vendorsrma')->__('Message'),
          'name'      => 'request[comment]',
          'wysiwyg' => false,
          'style' => 'width:700px; height:250px;',
          'required'=>false,
      ));


      $file=$fieldset_3->addField('ticket[file]', 'text', array(
          'value'  => 'file',
          'tabindex' => 1
      ));

      $file->setRenderer($this->getLayout()->createBlock('vendorsrma/vendor_request_edit_renderer_file'));



      if ( Mage::registry('current_request') ) {
          $form->setValues(Mage::registry('current_request')->getData());
      }
      return parent::_prepareForm();
  }
}