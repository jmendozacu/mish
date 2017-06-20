<?php

class VES_VendorsMap_Block_Vendor_Account_New extends Mage_Directory_Block_Data{
	public function __construct(){
		$this->setTemplate("ves_vendorsmap/map/new.phtml");
	}

    public function getUrlNew(){
        return $this->getUrl("vendors/vendor_map/save");
    }

    public function getUrlUpload(){
        return $this->getUrl('vendors/vendor_map/upload',array("vendor_id"=>$this->getVendors()->getId()));
    }

    public function getVendors(){
        return Mage::getSingleton('vendors/session')->getVendor();
    }

    public function getOptionTypeMap(){
        return Mage::getModel('vendorsmap/map')->getOptionTypeMap();
    }
}