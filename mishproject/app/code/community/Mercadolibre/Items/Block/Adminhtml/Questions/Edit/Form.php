<?php

class Mercadolibre_Items_Block_Adminhtml_Questions_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      //get current store ID
	  if($this->getRequest()->getParam('store')){
			$storeId = (int) $this->getRequest()->getParam('store');
		} else if(Mage::helper('items')-> getMlDefaultStoreId()){
			$storeId = Mage::helper('items')-> getMlDefaultStoreId();
		} else {
			$storeId = $this->getStoreId();
		}
	  
	  $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}