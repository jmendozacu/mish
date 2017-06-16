<?php

class VES_VendorsCms_Block_Vendor_App_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
									  'id' => 'edit_form',
									  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'),'type'=>$this->getRequest()->getParam('type'))),
									  'method' => 'post',
									  'enctype' => 'multipart/form-data'
								   )
		);

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}