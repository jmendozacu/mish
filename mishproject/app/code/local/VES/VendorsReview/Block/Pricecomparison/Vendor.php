<?php
class VES_VendorsReview_Block_Pricecomparison_Vendor extends Mage_Core_Block_Template {
	protected $_review_collection;
	
	public function getVendorInfo() {
		return $this->getVendor();
	}
	/**
	 * Get review collection of current vendor
	 */
	public function getReviewCollection(){
		if(!isset($this->_review_collection[$this->getVendorId()])){
			$this->_review_collection[$this->getVendorId()] = Mage::getModel('vendorsreview/review')->getCollection()
										->addFieldToFilter('status',VES_VendorsReview_Model_Type::APPROVED)
										->addFieldToFilter('vendor_id',$this->getVendorId());
		}
		return $this->_review_collection[$this->getVendorId()];
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
		return ceil($total/$reviewCollection->count());
	}
	
	/**
	 * Get current vendor id
	 * @return int
	 */
	public function getVendorId() {
		return $this->getVendor()->getId();
	}
}