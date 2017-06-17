<?php
class VES_VendorsMap_Block_Map extends Mage_Directory_Block_Data
{
	public function getDefaultLocationLat(){
		$location = Mage::getStoreConfig('vendors/vendors_map/location_lat');
		return $location;
	}
	
	public function getDefaultLocationLng(){
		$location = Mage::getStoreConfig('vendors/vendors_map/location_lng');
		return $location;
	}
	
	public function getOptionKmMap(){
		return Mage::getModel("vendorsmap/map")->getOptionKmMap();
	}

    public function getOptionTypeMap(){
        return Mage::getModel("vendorsmap/map")->getOptionTypeMap();
    }
	public function getHeader(){
		return Mage::getStoreConfig('vendors/vendors_map/heading');
	}
	
	
	public function getSubHeader(){
		return Mage::getStoreConfig('vendors/vendors_map/sub_heading');
	}
}