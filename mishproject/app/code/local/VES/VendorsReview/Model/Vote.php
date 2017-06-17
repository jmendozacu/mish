<?php

class VES_VendorsReview_Model_Vote extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsreview/vote');
    }
    
    public function getVoteByReview($reviewId) {
    	$resource = Mage::getSingleton('core/resource');
    	$readConnection = $resource->getConnection('core_read');
    	$table = $resource->getTableName('vendorsreview/vote');
    	if($reviewId != null) $select = $readConnection->select()->from($table, array('*'))->where('review_id = ?', $reviewId);
    	else $select = $readConnection->select()->from($table, array('*'));
    	$rowsArray = $readConnection->fetchAll($select);
    
    	return $rowsArray;
    }
    
    public function getVoteRating($review, $rating=null) {
    	$vote = $this->getVoteByReview($review);
    	if($rating) {
    		foreach($vote as $_v) {
    			if($_v['rating_id'] == $rating) return $_v;
    		}
    	}
    	return $vote;
    }
}