<?php

class VES_VendorsSubAccount_Block_Vendor_Profile_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendorssubaccount_form', array('legend'=>Mage::helper('vendorssubaccount')->__('Account information')));
		$fieldset->addField('username', 'label', array(
			'label'     => Mage::helper('vendorssubaccount')->__('User Name'),
			'name'      => 'username',
			'style'		=> 'width: 100px',
			'before_element_html'	=> Mage::getSingleton('vendors/session')->getVendor()->getVendorId().'_',
		));
		
		$fieldset->addField('firstname', 'text', array(
			'label'     => Mage::helper('vendorssubaccount')->__('First Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'firstname',
		));
		$fieldset->addField('lastname', 'text', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Last Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'lastname',
		));
		$fieldset->addField('email', 'text', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Email'),
			'class'     => 'required-entry validate-email',
			'required'  => true,
			'name'      => 'email',
		));
		$fieldset->addField('telephone', 'text', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Telephone'),
			'class'     => 'validate-phoneStrict',
			'name'      => 'telephone',
		));

		
		$fieldset1 = $form->addFieldset('password_fieldset', array('legend'=>Mage::helper('vendorssubaccount')->__('Password')));
		$fieldset1->addField('password', 'password', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Password'),
			'class'		=> 'validate-password',
			'name'      => 'password',
		));
		$fieldset1->addField('confirmation', 'password', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Confirm Password'),
			'class'		=> 'validate-cpassword',
			'name'      => 'confirmation',
		));
		if ( Mage::getSingleton('vendors/session')->getVendorsSubAccountData() )
		{
			$form->setValues(Mage::getSingleton('vendors/session')->getVendorsSubAccountData());
			Mage::getSingleton('vendors/session')->setVendorsSubAccountData(null);
		} elseif ( Mage::registry('vendorssubaccount_data') ) {
			$form->setValues(Mage::registry('vendorssubaccount_data')->getData());
		}
		return parent::_prepareForm();
	}
}