<?php


class VES_VendorsReview_Block_Customer_Account_Notification extends Mage_Core_Block_Template
{
	
    public function getCustomerName()
    {
        return $this->getCustomer()->getName();
    }
    
    public function getCustomer() {
    	return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function getCustomerRatingLinks() {
    	$data = Mage::getModel('vendorsreview/link')->getShowLinkByVendorCustomer($this->getCustomer()->getId());
    	$links = array();
    	
    	foreach($data as $_link) {
    		if($_link['show_rating_link'] == '1') {
    			$order_ic = Mage::getModel('sales/order')->load($_link['order_id'])->getIncrementId();
    			$vendor = Mage::getModel('vendors/vendor')->load($_link['vendor_id']);
    			$result['link'] = $this->getVendorRatingUrl($vendor->getVendorId(), $_link['order_id']);
    			$result['order_ic'] = $order_ic;
    			$result['order_id'] = $_link['order_id'];
    			$result['vendor'] = $vendor;
    			$links[] = $result;
    		}
    	}
    	return $links;
    }
    
    public function isShow() {
    	if(count($this->getCustomerRatingLinks())) return true;
    	return false;
    }
    
    public function getVendorRatingUrl($vendor,$order) {
    	return Mage::helper('vendorsreview')->getVendorRatingUrl($vendor,$order);
    }
    
    
    public function getHiddenUrl() {
    	return $this->getUrl('vendorsreview/ajax/hidden');
    }
}
