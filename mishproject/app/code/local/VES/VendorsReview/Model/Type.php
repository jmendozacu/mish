<?php

class VES_VendorsReview_Model_Type extends Varien_Object
{
   // const PENDING			= 1;
    const APPROVED			= 1;
    const NOT_APPROVED		= 2;
    
    static public function toOptionArray()
    {
        return array(
          //  self::PENDING    		=> Mage::helper('vendorsreview')->__('Pending'),
            self::APPROVED   		=> Mage::helper('vendorsreview')->__('Approved'),
        	self::NOT_APPROVED   	=> Mage::helper('vendorsreview')->__('Not-Approved')
        );
    }
    
    public function getType($type) {
    	switch($type) {
    	//	case 'pending': return self::PENDING; break;
    		case 'approved': return self::APPROVED; break;
    		case 'not_approved': return self::NOT_APPROVED; break;
    	}
    }
}