<?php

class OTTO_AdvancedFaq_Block_Seller_Faq_Grid extends OTTO_AdvancedFaq_Block_Adminhtml_Faq_Grid
{

  protected function _prepareCollection()
  {
  	  $tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('advancedfaq/category');
  	  $collection = Mage::getModel('advancedfaq/faq')->getCollection();
  	  $sellerId = Mage::getSingleton('sellers/session')->getSellerId();
  	  $collection->addFieldToFilter('seller_id',$sellerId);
      $this->setCollection($collection);
      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }

}