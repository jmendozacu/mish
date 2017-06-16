<?php

class VES_VendorsMap_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getDefaultLocation(){
		$marker_id =  Mage::helper('vendorsconfig')->getVendorConfig('vendorsmap/map/laltng',Mage::registry('current_vendor')->getId());
		$marker = Mage::getModel('vendorsmap/map')->load($marker_id);
		if($marker->getId()){
			return $marker->getData("position");
		}
		else{
			return null;
		}
	}
	
	public function getDefaultLocationVendor(){
		$marker_id =  Mage::helper('vendorsconfig')->getVendorConfig('vendorsmap/map/laltng', Mage::getSingleton('vendors/session')->getVendorId());
		$marker = Mage::getModel('vendorsmap/map')->load($marker_id);
		if($marker->getId()){
			return $marker->getData("position");
		}
		else{
			return null;
		}
	}


    public function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function getRegionName($code){
        $region = Mage::getModel('directory/region')->load($code);
        return $region->getName();
    }

    public function getCountryName($code){
        $region = Mage::getModel('directory/country')->load($code);
        return $region->getName();
    }
}