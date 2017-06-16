<?php

class VES_VendorsLiveChat_Block_Vendor_History_Edit_Tab_Side extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        if($this->getRequest()->getParam('id')){
            $this->setTemplate('ves_vendorslivechat/renderer/side.phtml');
        }
    }
    public function getMessages(){
        $id = $this->getBoxData()->getId();
        $messages = Mage::getModel("vendorslivechat/message")->getCollection()->addFieldToFilter("box_id",$id);
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
        if($this->getBoxData()) return $this->getBoxData()->getIP();
        return null;
    }

    public function getGeoIp(){
        $ip = $this->getBoxIp();
        $geo= new VES_VendorsLiveChat_Helper_Geoip();
        $geo->setRecord('GeoLiteCity',$ip);
        return $geo;
    }
}