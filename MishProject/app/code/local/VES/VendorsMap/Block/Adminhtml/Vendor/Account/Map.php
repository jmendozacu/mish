<?php

class VES_VendorsMap_Block_Adminhtml_Vendor_Account_Map extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function __construct(){
        $this->setTemplate("ves_vendorsmap/adminhtml/map.phtml");
    }
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    public function getVendors(){
        $id = $this->getRequest()->getParam("id");
        return Mage::getModel('vendors/vendor')->load($id);
    }

    public function getDefaultLocationLat(){
        $location = Mage::getStoreConfig('vendors/vendors_map/location_lat');
        return $location;
    }

    public function getDefaultLocationLng(){
        $location = Mage::getStoreConfig('vendors/vendors_map/location_lng');
        return $location;
    }

    public function getMapCollection(){
        $maps = Mage::getModel('vendorsmap/map')->getCollection()->addFieldToFilter("vendor_id",$this->getVendors()->getId());
        return $maps;
    }


    public function getListAddressHtml(){
        $block = $this->getLayout()->createBlock("vendorsmap/adminhtml_vendor_map_sidebar")->setMap($this->getMapCollection());
        return $block->toHtml();
    }

    public function getFormNewHtml(){
        $block = $this->getLayout()->createBlock("vendorsmap/adminhtml_vendor_map_new");
        return $block->toHtml();
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

    public function getRegionName($code){
        $region = Mage::getModel('directory/region')->load($code);
        return $region->getName();
    }

    public function getCountryName($code){
        $region = Mage::getModel('directory/country')->load($code);
        return $region->getName();
    }

    public function getScriptHtml(){
        $maps = $this->getMapCollection();
        $script = '<script type="text/javascript">';
        $script .=	'var neighborhoods = new Array();var messages = new Array();var infowindows = new Array();';
        $i = 0;
        foreach($maps as $map){
            $position = explode('|',$map->getData("position"));
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
            $script .= 'neighborhoods['.$i.'] =  new google.maps.LatLng('.$position[0].','.$position[1].');';
            $script .= 'messages['.$i.'] = '.$json_data.';';
            $i++;
        }
        $script .= '</script>'	;
        return $script;
    }
}