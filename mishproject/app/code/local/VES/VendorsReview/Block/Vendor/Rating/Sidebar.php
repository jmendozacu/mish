<?php
class VES_VendorsReview_Block_Vendor_Rating_Sidebar extends Mage_Core_Block_Template {
	protected $_review_collection;
	
	public function getVendorInfo() {
		return Mage::getModel('vendors/vendor')->loadByVendorId(Mage::registry('vendor_id'));
	}
	/**
	 * Get review collection of current vendor
	 */
	public function getReviewCollection(){
		if(!isset($this->_review_collection)){
			$this->_review_collection = Mage::getModel('vendorsreview/review')->getCollection()
										->addFieldToFilter('status',VES_VendorsReview_Model_Type::APPROVED)
										->addFieldToFilter('vendor_id',$this->getVendorId());
		}
		return $this->_review_collection;
	}
	
	/**
	 * Get review count
	 */
	public function getReviewCount(){
		return $this->getReviewCollection()->count();
	}
	/**
	 * Get vendor rating score
	 */
	public function getRatingScore() {
		$reviewCollection = $this->getReviewCollection();
		$total = 0;
		foreach($reviewCollection as $_review) {
			$total += $_review->getSummaryPercent();
		}
		return $total?ceil($total/$reviewCollection->count()):0;
	}
	/**
	 * Get current vendor
	 * @return VES_Vendors_Model_Vendor
	 */
	public function getVendor(){
		if(!$this->getData('vendor')){
			$this->setData('vendor',Mage::registry('vendor'));
		}
		return $this->getData('vendor');
	}
	
	/**
	 * Get current vendor id
	 * @return int
	 */
	public function getVendorId() {
		return $this->getVendor()->getId();
	}
}