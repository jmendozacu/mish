<?php
class VES_VendorsLiveChat_Block_Vendor_History_Renderer_View extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
    public function __construct()
    {
        $this->setTemplate('ves_vendorslivechat/renderer/view.phtml');
    }
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    public function getMessages(){
         $id = $this->getBoxData()->getId();
         $messages = Mage::getModel("vendorslivechat/message")->getCollection()->addFieldToFilter("session_id",$id);
         return $messages;
    }
    public function getBoxData(){
        if(Mage::registry("box_data")) return Mage::registry("box_data");
        return null;
    }
    public function getClassNameMessageBox($type){
        return Mage::getModel("vendorslivechat/message_type")->getTypeNameVendor($type);
    }


    public function getBoxIp(){
        if($this->getBoxData()) return $this->getBoxData()->getIp();
        return null;
    }

    public function getGeoIp(){
        $ip = $this->getBoxIp();
        $geo= new VES_VendorsLiveChat_Helper_Geoip();
        $geo->setRecord('GeoLiteCity',$ip);
        return $geo;
    }
}
