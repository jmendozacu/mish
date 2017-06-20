<?php
/**
 * Created by PhpStorm.
 * User: December January
 * Date: 3/25/14
 * Time: 12:23 PM
 */

class VES_VendorsFlatrate_Model_Observer {
	
	public function ves_vendorsconfig_form_fieldset_prepare_before(Varien_Event_Observer $observer){
    	$fieldsetId = $observer->getEvent()->getGroupId();
    	if(!Mage::helper('vendorsflatrate')->enableFlatrateShipping()){
    		if(in_array($fieldsetId,array('shipping_flatrate'))){
    			$group	= $observer->getEvent()->getGroup();
    			$fields = $group->getFields();
    			$group->setData('fields',array());
    		}
    		return;
    	}
    }
}