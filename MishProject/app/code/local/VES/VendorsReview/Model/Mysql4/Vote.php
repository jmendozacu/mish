<?php

class VES_VendorsReview_Model_Mysql4_Vote extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsreview/vote', 'entity_id');
    }
}