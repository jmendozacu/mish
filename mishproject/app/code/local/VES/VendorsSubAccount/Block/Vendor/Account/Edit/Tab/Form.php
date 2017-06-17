<?php

class VES_VendorsSubAccount_Block_Vendor_Account_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendorssubaccount_form', array('legend'=>Mage::helper('vendorssubaccount')->__('User Information')));
		$fieldset->addType('username','VES_VendorsSubAccount_Block_Form_Element_Username');
		$vendor = Mage::getSingleton('vendors/session')->getVendor();
		$fieldset->addField('username', 'username', array(
			'label'     => Mage::helper('vendorssubaccount')->__('User Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'username',
			'style'		=> 'width: 100px',
			'before_element_html'	=> $vendor->getVendorId().VES_VendorsSubaccount_Model_Account::SPECIAL_CHAR,
		));
		
		$fieldset->addField('role_id', 'select', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Role'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'role_id',
			'values'    => Mage::getModel('vendorssubaccount/source_role')->toOptionArray($vendor->getId()),
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
		$field = $fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Status'),
			'name'      => 'status',
			'values'    => Mage::getModel('vendorssubaccount/status')->toOptionArray(),
		));
		if(!Mage::registry('vendorssubaccount_data')->getId()){
			Mage::registry('vendorssubaccount_data')->setStatus(VES_VendorsSubAccount_Model_Account::STATUS_ENABLED);
		}
		
		$fieldset1 = $form->addFieldset('password_fieldset', array('legend'=>Mage::helper('vendorssubaccount')->__('Password')));
		$fieldset1->addField('password', 'password', array(
			'label'     => Mage::helper('vendorssubaccount')->__('Password'),
			'required'  => !Mage::registry('vendorssubaccount_data')->getId(),
			'name'      => 'password',
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