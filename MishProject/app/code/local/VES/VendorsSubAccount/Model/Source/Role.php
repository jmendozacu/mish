<?php

class VES_VendorsSubAccount_Model_Source_Role extends Varien_Object
{

    static public function getOptionArray($vendorId)
    {
    	$options = array();
    	$collection = Mage::getModel('vendorssubaccount/role')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
    	foreach($collection as $role){
    		$options[$role->getId()] = $role->getRoleName();
    	}
        return $options;
    }
    
	static public function toOptionArray($vendorId)
    {
    	$options = array();
    	$collection = Mage::getModel('vendorssubaccount/role')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
    	$options[] = array('value'=>'','label'=>Mage::helper('vendorssubaccount')->__('-- Please Select --'));
    	foreach($collection as $role){
    		$options[] = array('value'=>$role->getId(),'label'=>$role->getRoleName());
    	}
        return $options;
        return $options;
    }
}