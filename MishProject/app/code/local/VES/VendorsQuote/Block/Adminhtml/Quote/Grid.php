<?php

class VES_VendorsQuote_Block_Adminhtml_Quote_Grid extends VES_VendorsQuote_Block_Vendor_Quote_Grid
{
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendorsquote/quote')->getCollection()
		              ->addFieldToFilter('status',array('nin'=>array(VES_VendorsQuote_Model_Quote::STATUS_CREATED)));
		$this->setCollection($collection);
		return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	}

}