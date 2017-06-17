<?php
/**
 * Created by PhpStorm.
 * User: December January
 * Date: 3/25/14
 * Time: 12:23 PM
 */

class VES_VendorsShippingUPS_Model_Observer {
	
	public function ves_vendorsconfig_form_fieldset_prepare_before(Varien_Event_Observer $observer){
    	$fieldsetId = $observer->getEvent()->getGroupId();
    	if(!Mage::getStoreConfig('carriers/ups/active')){
    		if(in_array($fieldsetId,array('shipping_ups'))){
    			$group	= $observer->getEvent()->getGroup();
    			$fields = $group->getFields();
    			$group->setData('fields',array());
    		}
    		return;
    	}
    }
}