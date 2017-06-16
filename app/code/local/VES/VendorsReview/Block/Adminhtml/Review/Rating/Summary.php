<?php


class VES_VendorsReview_Block_Adminhtml_Review_Rating_Summary extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('ves_vendorsreview/rating/stars/summary.phtml');
        $this->setReviewId(Mage::registry('review_data')->getId());
    }

    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            if( Mage::registry('review_data') ) {
                $ratingCollection = Mage::getModel('vendorsreview/rating')->getCollection()
                ->setOrder('position','asc');
            }
            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }
    
    public function getVote($rating=null) {
    	$vote = Mage::getModel('vendorsreview/vote')->getVoteByReview($this->getReviewId());
    	if($rating) {
    		foreach($vote as $_v) {
    			if($_v['rating_id'] == $rating) return $_v;
    		}
    	}
    	return $vote;
    }

    public function getRatingSummaryScore()
    {
    	$total = 0;
    	$count = $this->getRating()->count();
        foreach($this->getRating() as $_rating) {
        	$vote = $this->getVote($_rating->getId());
        	$total += $vote['rate_percents'];
        }
        return ceil($total/$count);
    }
}
