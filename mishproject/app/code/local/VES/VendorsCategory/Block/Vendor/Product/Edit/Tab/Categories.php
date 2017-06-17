<?php
/**
 * Product categories tab
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsCategory_Block_Vendor_Product_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorscategory')->__('Categories'),'class'=>'fieldset-wide'));
		$fieldset->addField('vendor_categories', 'multiselect', array(
      		'label'     => Mage::helper('vendors')->__('Category'),
      		'required'  => false,
			'style'		=> 'width:100%;height: 300px;',
      		'name'      => 'product[vendor_categories]',
			'values'	=> Mage::getModel('vendorscategory/source_category')->getTreeOptions(Mage::getSingleton('vendors/session')->getVendorId(),false),
      	));
		if ( Mage::getSingleton('adminhtml/session')->getVendorsData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
			Mage::getSingleton('adminhtml/session')->setVendorsData(null);
		} elseif ( Mage::registry('product') ) {
			$form->setValues(Mage::registry('product')->getData());
		}
		return parent::_prepareForm();
	}
}
