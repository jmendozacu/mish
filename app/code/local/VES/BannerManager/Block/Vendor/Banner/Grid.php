<?php

class VES_BannerManager_Block_Vendor_Banner_Grid extends VES_BannerManager_Block_Adminhtml_Banner_Grid
{
	protected function _prepareCollection(){
		$vendorId = Mage::getSingleton('vendors/session')->getVendorId();
		$collection = Mage::getModel('bannermanager/banner')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
		$this->setCollection($collection);
		return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	}
}