<?php

class VES_Vendors_Block_Adminhtml_Vendors_Edit_Tab_Bank extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendors_form_main', array('legend'=>Mage::helper('vendors')->__('Bank Data')));

		$model = Mage::getModel('vendors/additional')->load($this->getRequest()->getParam('id'),'vendor_id');
		$result = Mage::helper('core')->jsonDecode($model->getBankData());	

		$fieldset->addField('work_with_bill', 'label', array(
			'label'     => Mage::helper('vendors')->__('Work with Bills or Invoices : '),
			'name'      => 'work_with_bill',
			'value'    => ($result['work_with_bill']==1)?'Yes':'No',
		));	

		$fieldset->addField('name_of_bank', 'label', array(
			'label'     => Mage::helper('vendors')->__('Name Of Bank : '),
			'name'      => 'name_of_bank',
			'value'    => $result['name_of_bank'],
		));

		$fieldset->addField('bank_details', 'label', array(
			'label'     => Mage::helper('vendors')->__('Account Type : '),
			'name'      => 'bank_details',
			'value'    => $result['bank_details'],
		));

		$fieldset->addField('account_number', 'label', array(
			'label'     => Mage::helper('vendors')->__('Account No. : '),
			'name'      => 'account_number',
			'value'    => $result['account_number'],
		));	
	}
}
