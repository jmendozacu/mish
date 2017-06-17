<?php
class VES_VendorsPriceComparison2_Block_Vendor_Selectandsell_Edit_Form_Element_Option extends Varien_Data_Form_Element_Abstract
{
	public function __construct($attributes=array())
	{
		parent::__construct($attributes);
		$this->setType('note');
	}
	/**
	 * Retrieve Element HTML
	 *
	 * @return string
	 */
	public function getHtml()
	{
	    $product = $this->getProduct();
		$html = '<tr><td>';
		$block = Mage::app()->getLayout()->createBlock('pricecomparison2/vendor_selectandsell_edit_form_element_option_table');
		$block->setProduct($product);
		$block->setPriceComparison($this->getPricecomparison());
		$html .= $block->toHtml();
		$html .='</td></tr>';
		return $html;
	}
}