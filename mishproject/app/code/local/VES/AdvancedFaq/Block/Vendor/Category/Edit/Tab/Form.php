<?php

class OTTO_AdvancedFaq_Block_Seller_Category_Edit_Tab_Form extends OTTO_AdvancedFaq_Block_Adminhtml_Category_Edit_Tab_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('category_form', array('legend'=>Mage::helper('advancedfaq')->__('Topic information')));
		 
		$fieldset->addField('title', 'text', array(
				'label'     => Mage::helper('advancedfaq')->__('Title'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'title',
		));
		$fieldset->addField('url_key', 'text', array(
				'label'     => Mage::helper('advancedfaq')->__('Url Key'),
				'class'     => 'validate-xml-identifier',
				'required'  => true,
				'name'      => 'url_key',
		));
		
	
		$fieldset->addField('status', 'select', array(
				'label'     => Mage::helper('advancedfaq')->__('Show on main page'),
				'name'      => 'status',
				'values'    => OTTO_AdvancedFaq_Model_Status::getStatusShowArray()
		));
	
	
		$fieldset->addField('sort_order', 'text', array(
				'label'     => Mage::helper('advancedfaq')->__('Sort Order'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'sort_order',
		));
		/*
		 $fieldset->addField('description', 'textarea', array(
		 		'label'     => Mage::helper('kbase')->__('Content'),
		 		'name'      => 'description',
		 		'style' => 'width:600px; height:300px;',
		 ));
		*/
		if ( Mage::getSingleton('sellers/session')->getCategoryData() )
		{
			$form->setValues(Mage::getSingleton('sellers/session')->getCategoryData());
			Mage::getSingleton('sellers/session')->setCategoryData(null);
		} elseif ( Mage::registry('category_data') ) {
			$form->setValues(Mage::registry('category_data')->getData());
		}
		return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
	}
}