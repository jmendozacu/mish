<?php

class VES_Vendors_Block_Adminhtml_Vendors_Edit_Tab_Terms extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendors_form_main', array('legend'=>Mage::helper('vendors')->__('MISH CONDITIONS AND CONTRACT')));
		
		$model = Mage::getModel('vendors/additional')->load($this->getRequest()->getParam('id'),'vendor_id');
		$terms = ($model->getAcceptTerms()==2)?'Rejected':(($model->getAcceptTerms()==1)?'Accepted':'In Progress');
		$fieldset->addField('terms', 'label', array(
			'label'     => Mage::helper('vendors')->__('Terms Status : '),
			'name'      => 'terms',
			'value'    => $terms,
		));		
	}
}
