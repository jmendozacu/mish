<?php

class VES_VendorsMap_Block_Shop_List extends Mage_Core_Block_Template{
    public function __construct(){
        $this->setTemplate("ves_vendorsmap/shop/list.phtml");
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

    public function getMapCollection(){
        $maps = Mage::getModel('vendorsmap/map')->getCollection()->addFieldToFilter("vendor_id",$this->getVendors()->getId());
        return $maps;
    }


    public function getVendors(){
        return Mage::registry("current_vendor");
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
