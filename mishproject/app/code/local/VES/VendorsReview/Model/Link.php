<?php

class VES_VendorsReview_Model_Link extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsreview/link');
    }
    
    public function getShowLinkByVendorCustomer($customer,$vendor=null) {
    	$resource = Mage::getSingleton('core/resource');
    	$readConnection = $resource->getConnection('core_read');
    	$table = $resource->getTableName('vendorsreview/link');
    	if($vendor) $select = $readConnection->select()->from($table, array('*'))->where('vendor_id = ?', $vendor)
    	->where('customer_id = ?', $customer);
    	
    	else $select = $readConnection->select()->from($table, array('*'))->where('customer_id = ?', $customer);
    	
    	$rowsArray = $readConnection->fetchAll($select);
    
    	return $rowsArray;
    }
    


}