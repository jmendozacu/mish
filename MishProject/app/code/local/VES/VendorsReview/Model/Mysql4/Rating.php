<?php

class VES_VendorsReview_Model_Mysql4_Rating extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsreview/rating', 'rating_id');
    }
}