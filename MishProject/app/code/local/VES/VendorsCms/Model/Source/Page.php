<?php

class VES_VendorsCms_Model_Source_Page
{
	public function toOptionArray()
    {
    	$pageCollection = Mage::getModel('vendorscms/page')->getCollection()->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendorId());
    	$options = array();
    	$options[] = array('label'=>Mage::helper('vendorscms')->__('-- Do not apply --'),'value'=>'');
    	
    	foreach($pageCollection as $page){
    		$options[] = array('label'=>$page->getTitle(),'value'=>$page->getIdentifier());
    	}
    	return $options;
    }
}