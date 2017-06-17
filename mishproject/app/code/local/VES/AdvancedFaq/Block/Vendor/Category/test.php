<?php

class OTTO_AdvancedFaq_Block_Seller_Category_Grid extends OTTO_AdvancedFaq_Block_Adminhtml_Category_Grid
{

	public function __construct()
	{
		parent::__construct();
	}
  protected function _prepareCollection()
  {
      $collection = Mage::getModel('advancedfaq/category')->getCollection();
      $sellerId = Mage::getSingleton('sellers/session')->getSellerId();
      $collection->addFieldToFilter('seller_id',$sellerId);
      $this->setCollection($collection);
      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }
}