<?php

class VES_VendorsMemberShip_Model_Source_Expiryaction
{
	static public function toOptionArray()
    {
    	$result = array(array('label'=> Mage::helper('vendors')->__('Disable vendor account'),'value'=>'disable_account'));
    	foreach(Mage::getModel('vendors/group')->getCollection() as $group){
    		$result[] = array(
    			'label'	=> Mage::helper('vendors')->__('Change vendor group to "%s"',$group->getName()),
    			'value' => "change_group||".$group->getId(),
    		);
    	}
    	return $result;
    }

}