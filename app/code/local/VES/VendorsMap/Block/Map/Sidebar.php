<?php
class VES_VendorsMap_Block_Map_Sidebar extends Mage_Core_Block_Template {
	protected $_map_collection;
	
	public function getVendorInfo() {
		return Mage::getModel('vendors/vendor')->loadByVendorId(Mage::registry('vendor_id'));
	}
	/**
	 * Get review collection of current vendor
	 */
	public function getMapCollection(){
		if(!isset($this->_map_collection)){
			$this->_map_collection = Mage::getModel('vendorsmap/map')->getCollection()
										->addFieldToFilter('vendor_id',$this->getVendorId());
		}
		return $this->_map_collection;
	}
	
	public function isEnable(){
		if(sizeof($this->getMapCollection()) != 0) return true;
		return false;
	}
	/**
	 * Get review count
	 */
	public function getMarkerCount(){
		return $this->getReviewCollection()->count();
	}
	
	/**
	 * Get current vendor
	 * @return VES_Vendors_Model_Vendor
	 */
	public function getVendor(){
		return Mage::registry('vendor');
	}
	
	/**
	 * Get current vendor id
	 * @return int
	 */
	public function getVendorId() {
		return $this->getVendor()->getId();
	}
}