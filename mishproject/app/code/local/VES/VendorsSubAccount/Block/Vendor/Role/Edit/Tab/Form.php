<?php

class VES_VendorsSubAccount_Block_Vendor_Role_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendorssubaccount_form', array('legend'=>Mage::helper('vendorssubaccount')->__('Role Information')));
		$fieldset->addField('role_name', 'text', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Role Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'role_name',
		));
		if ( Mage::getSingleton('vendors/session')->getVendorsSubAccountData() )
		{
			$form->setValues(Mage::getSingleton('vendors/session')->getVendorsSubAccountData());
			Mage::getSingleton('vendors/session')->setVendorsSubAccountData(null);
		} elseif ( Mage::registry('role_data') ) {
			$form->setValues(Mage::registry('role_data')->getData());
		}
		return parent::_prepareForm();
	}
}