<?php

class VES_VendorsCms_Block_Vendor_Block_Grid extends Mage_Adminhtml_Block_Cms_Block_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsBlockGrid');
  }

  protected function _prepareCollection()
  {
  	  $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
      $collection = Mage::getModel('vendorscms/block')->getCollection()
      				->addFieldToFilter('vendor_id',$vendorId)
      				;
      $this->setCollection($collection);
      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      return parent::_prepareColumns();
  }
}