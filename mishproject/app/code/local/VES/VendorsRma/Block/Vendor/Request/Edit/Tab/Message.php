<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_Message extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);

      $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
      ->setTemplate('ves_vendorsrma/fieldset.phtml');

      $fieldset2 = $form->addFieldset('request_form_message', array('legend'=>Mage::helper('vendorsrma')->__('Request')))->setRenderer($renderer);;

      $message=$fieldset2->addField('vendorsrma', 'text', array(
      		'label'     => Mage::helper('vendorsrma')->__('Comment'),
      		'name'      => 'request[comment]',

      ));
      $message->setRenderer($this->getLayout()->createBlock('vendorsrma/vendor_request_edit_renderer_message'));


      if ( Mage::registry('current_request') ) {
          $form->setValues(Mage::registry('current_request')->getData());
      }


      return parent::_prepareForm();
  }

  

  
}

