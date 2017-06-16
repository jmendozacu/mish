<?php

class VES_VendorsReview_Block_Adminhtml_Rating_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		//rating title fieldset
		$fieldset = $form->addFieldset('rating_form', array('legend'=>Mage::helper('vendorsreview')->__('Rating information'),'class'=>'fieldset-wide'));
	 
		$fieldset->addField('title', 'text', array(
			'label'     => Mage::helper('vendorsreview')->__('Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'title',
		));

		$fieldset->addField('position', 'text', array(
			'label'     => Mage::helper('vendorsreview')->__('Sort order'),
			'name'      => 'position',
		));

		if ( Mage::getSingleton('vendors/session')->getFormData() )
		{
			$form->setValues(Mage::getSingleton('vendors/session')->getFormData());
			Mage::getSingleton('vendors/session')->setFormData(null);
		} elseif ( Mage::registry('rating_data') ) {
			$form->setValues(Mage::registry('rating_data')->getData());
		}
		return parent::_prepareForm();
	}
}