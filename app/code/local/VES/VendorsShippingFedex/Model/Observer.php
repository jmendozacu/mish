<?php

class VES_VendorsShippingFedex_Model_Observer {
	
	public function ves_vendorsconfig_form_fieldset_prepare_before(Varien_Event_Observer $observer){
    	$fieldsetId = $observer->getEvent()->getGroupId();
    	if(!Mage::getStoreConfig('carriers/fedex/active')){
    		if(in_array($fieldsetId,array('shipping_fedex'))){
    			$group	= $observer->getEvent()->getGroup();
    			$fields = $group->getFields();
    			$group->setData('fields',array());
    		}
    		return;
    	}
    }
}