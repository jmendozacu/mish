<?php


class VES_VendorsReview_Block_Adminhtml_Review_Rating_Detailed extends Mage_Adminhtml_Block_Template
{
    protected $_voteCollection = false;
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendorsreview/rating/detailed.phtml');
        if( Mage::registry('review_data') ) {
            $this->setReviewId(Mage::registry('review_data')->getReviewId());
        }
    }
    
    public function isAdminMode() {
    	return Mage::registry('useAdminMode');
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

    public function isSelected($option, $rating)
    {
        
    }
}
