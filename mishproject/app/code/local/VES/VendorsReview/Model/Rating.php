<?php

class VES_VendorsReview_Model_Rating extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsreview/rating');
    }
    
    public function getAllRatingIds() {
    	$collection = $this->getCollection()->setOrder('position','asc');
    	foreach($collection as $_rating) {
    		$result[] = $_rating->getId();
    	}
    	
    	return $result;
    }
}