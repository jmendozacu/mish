<?php

class Mercadolibre_Items_Block_Adminhtml_Shippingprofile_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

		$form->setUseContainer(true);
		$this->setForm($form);
		
		
		$CommonModel = Mage::getModel('items/common');
		$optionsShippingType = $CommonModel->getShippingProfileType(); 
		$optionsShippingTypeOption = $CommonModel->getShippingTypeOption(); 
		$this->setData('optionsShippingType',$optionsShippingType);
		$this->setData('optionsShippingTypeOption',$optionsShippingTypeOption);
		
		$this->setTemplate('items/shipping/new/created.phtml');	
		return parent::_prepareForm();
	  
  }
}