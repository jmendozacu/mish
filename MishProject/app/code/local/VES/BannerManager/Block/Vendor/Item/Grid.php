<?php
class VES_BannerManager_Block_Vendor_Item_Grid extends VES_BannerManager_Block_Adminhtml_Item_Grid{
	protected function _prepareColumns(){
		parent::_prepareColumns();
		$vendorId = Mage::getSingleton('vendors/session')->getVendorId();
		$this->getColumn('banner_id')->setData('options',Mage::getModel('bannermanager/source_vendor_banner')->getOptionArray($vendorId));
	}
	
	protected function _prepareCollection()
  	{
  		$vendorId	= Mage::getSingleton('vendors/session')->getVendorId();
      	$collection = Mage::getModel('bannermanager/item')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
      	$this->setCollection($collection);
      	return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  	}
}