<?php

class VES_VendorsMap_Block_Vendor_Account_Sidebar extends Mage_Adminhtml_Block_Abstract{
	public function __construct(){
		$this->setTemplate("ves_vendorsmap/map/list.phtml");
	}
	public function getUrlImage($id){
		$map = Mage::getModel("vendorsmap/map")->load($id);
		if($map->getData("logo")){
			$url=  Mage::getBaseUrl('media').'ves_vendors/map/logo/vendor'.$map->getData("vendor_id")."/address".$map->getId().'/'.base64_decode($map->getData("logo"));
		}
		else{
			//Mage::getModel("vendor")
			$url = Mage::getBaseUrl('media').$this->getVendors()->getLogo();
		}
		return $url;
	}
	
	
	public function getVendors(){
		return Mage::getSingleton('vendors/session')->getVendor();
	}


    public function getRegionName($code){
        return Mage::helper("vendorsmap")->getRegionName($code);
    }

    public function getCountryName($code){
        return Mage::helper("vendorsmap")->getCountryName($code);
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