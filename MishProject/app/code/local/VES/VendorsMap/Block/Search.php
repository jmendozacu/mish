<?php

class VES_VendorsMap_Block_Search extends Mage_Core_Block_Template
{
	protected $_map_collection;

	public function getMapCollection(){
        $data = $this->getParamData();
		if(!isset($this->_map_collection)){
            $collection = Mage::getModel('vendorsmap/map')->getCollection()
			->addFieldToFilter('vendor_id', array('in' =>$this->getVendorId()));
            if($data["region_id"]){
                $collection->addFieldToFilter('region_id', array('eq' =>$data["region_id"]));
            }
            else{
                if($data["region"]){
                    $collection->addFieldToFilter("region",array("like"=>"%".trim($data["region"])."%"));
                }
            }
            if($data["country"]){
                $collection->addFieldToFilter('country_id', array('eq' =>$data["country"]));
            }
            if($data["attribute"]){
                $collection->addFieldToFilter('attribute', array('finset' =>$data["attribute"]));
            }
            if($data["zip"]){
                $collection->addFieldToFilter('postcode', array('eq' =>$data["zip"]));
            }
            if($data["city"]){
                $collection->addFieldToFilter("city",array("like"=>"%".trim($data["city"])."%"));
            }
            $this->_map_collection = $collection;
		}
		return $this->_map_collection;
	}

	public function getUrlImage($id,$vendor_id){
		$map = Mage::getModel("vendorsmap/map")->load($id);
		if($map->getData("logo")){
			$url=  Mage::getBaseUrl('media').'ves_vendors/map/logo/vendor'.$map->getData("vendor_id")."/address".$map->getId().'/'.base64_decode($map->getData("logo"));
		}
		else{
			$vendor = Mage::getModel('vendors/vendor')->load($vendor_id);
			$url = Mage::getBaseUrl('media').$vendor->getLogo();
		}
		return $url;
	}
	
	public function getMesssages(){
		$maps = $this->getMapCollection();
		$messages= array();
		foreach($maps as $map){
            if($map->getData("region_id")){
                $region = $this->getRegionName($map->getData("region_id"));
            }
            else{
                if($map->getData("region")){
                    $region = $map->getData("region");
                }
                else{
                    $region = null;
                }
            }
            $data = array($map->getData("title"),$map->getData("address"),$map->getData("city"),$region,$this->getCountryName($map->getData("country_id")),$map->getData("telephone"),$map->getData("postcode"));
			$json_data = json_encode($data);
			$messages[] =  $json_data;
		}
		return json_encode($messages);
	}
	
	public function getNeighborhoods(){
		$maps = $this->getMapCollection();
		$neighborhoods = array();
		foreach($maps as $map){
			$neighborhoods[] =  $map->getData("position");
		}
		return json_encode($neighborhoods);
	}

    public function getRegionName($code){
        $region = Mage::getModel('directory/region')->load($code);
        return $region->getName();
    }

    public function getCountryName($code){
        $region = Mage::getModel('directory/country')->load($code);
        return $region->getName();
    }

    public function getTypeNameMap($code){
        $name = null;
        $code = explode(",",$code);
        foreach($code as $c){
            $name .= Mage::getModel("vendorsmap/map")->getTypeNameMap($c).",";
        }
        return trim($name,",");
    }
}