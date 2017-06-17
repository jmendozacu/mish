<?php
/**
 * Backend for serialized array data
 *
 */
class VES_VendorsMap_Model_System_Config_Backend_Location extends Mage_Core_Model_Config_Data
{

	public function toOptionArray()
	{
		$data = array();
		$vendorId = Mage::getSingleton('vendors/session')->getVendorId();
		$collection = Mage::getModel('vendorsmap/map')->getCollection()->addFieldToFilter("vendor_id",$vendorId);
		$data[] =  array('value' =>"0",'label'=>Mage::helper("vendorsmap")->__("-- Select Default Location -- "));
		foreach($collection as $marker){
			$data[] = array('value' =>$marker->getId(),'label'=>$marker->getTitle());
		}
		return $data;
	}

}

