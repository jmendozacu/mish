<?php
class VES_VendorsReview_Model_Mysql4_Review_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
    {
    	parent::_construct();
        $this->_init('vendorsreview/review');
    }
}