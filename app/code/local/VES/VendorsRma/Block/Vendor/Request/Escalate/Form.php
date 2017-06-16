<?php

class VES_VendorsRma_Block_Vendor_Request_Escalate_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/saveNote', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );
      $this->setForm($form);
      $fieldset = $form->addFieldset('escalate_form', array('legend'=>Mage::helper('vendorsrma')->__('Notes')));
      $wysiwygConfig = Mage::getModel('vendorsrma/cms_wysiwyg_config')->getConfigSytem();
      $fieldset->addField('escalate_message', 'editor', array(
      		'label'     => Mage::helper('vendorsrma')->__('Message'),
      		'name'      => 'note[message]',
      		//'config' => $wysiwygConfig,
      		//'wysiwyg' => true,
      		'style' => 'width:700px; height:250px;',
			'required'=>true,
      ));
      

	  $file=$fieldset->addField('note[file]', 'text', array(
			'value'  => 'file',
			'tabindex' => 1
	  ));
	  
	  $file->setRenderer($this->getLayout()->createBlock('vendorsrma/vendor_request_escalate_renderer_file'));
      
      

      $form->setUseContainer(true);
     
      return parent::_prepareForm();
  }
  
  
  
}