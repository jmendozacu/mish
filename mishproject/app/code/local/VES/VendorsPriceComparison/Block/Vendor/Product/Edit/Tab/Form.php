<?php

class VES_VendorsPriceComparison_Block_Vendor_Product_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout(){
		$this->setChild('grid', $this->getLayout()->createBlock('pricecomparison/vendor_product'));
		$this->setTemplate('ves_vendorspricecomparison/form.phtml');
	}
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendors_form_main', array('legend'=>Mage::helper('vendors')->__('Select Product'),'class'=>'fieldset-wide'));

		$fieldset->addField('product_id', 'note', array(
			'text'	=> $this->getChildHtml('grid')
		));

		return parent::_prepareForm();
	}
}