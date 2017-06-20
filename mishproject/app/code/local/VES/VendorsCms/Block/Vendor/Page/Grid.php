<?php

class VES_VendorsCms_Block_Vendor_Page_Grid extends Mage_Adminhtml_Block_Cms_Page_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsPageGrid');
      $this->setDefaultSort('identifier');
      $this->setDefaultDir('ASC');
  }

  protected function _prepareCollection()
  {
  	  $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
      $collection = Mage::getModel('vendorscms/page')->getCollection()
      				->addFieldToFilter('vendor_id',$vendorId)
      				;
      $this->setCollection($collection);
      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      parent::_prepareColumns();
      $this->getColumn('page_actions')->setData('renderer','vendorscms/vendor_page_grid_renderer_action');
      return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
  }
}