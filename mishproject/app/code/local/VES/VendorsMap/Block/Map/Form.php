<?php
class VES_VendorsMap_Block_Map_Form extends Mage_Directory_Block_Data
{
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

    public function getUrlSave(){
        return $this->getUrl("vendors/vendor_map/saveformedit",array('id'=>$this->getMarker()->getId()));
    }

    public function getUrlUpload(){
        return $this->getUrl('vendors/vendor_map/upload',array("vendor_id"=>$this->getVendors()->getId()));
    }

    public function getOptionTypeMap(){
        return Mage::getModel('vendorsmap/map')->getOptionTypeMap();
    }
}