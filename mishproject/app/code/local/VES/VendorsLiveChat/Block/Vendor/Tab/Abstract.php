<?php
/**
 * Created by PhpStorm.
 * User: namnh_000
 * Date: 4/15/14
 * Time: 12:09 PM
 */

class VES_VendorsLiveChat_Block_Vendor_Tab_Abstract extends Mage_Adminhtml_Block_Abstract
{

    public function getSessionBox(){
        if($this->getSessionId()){
            $box = Mage::getModel("vendorslivechat/session")->load($this->getSessionId());
            return $box;
        }
        else{
            return null;
        }
    }

    public function getVendors(){
        return Mage::getSingleton('vendors/session')->getVendor();
    }

    
    public function getMessages(){
        if($this->getSessionBox()){
            $date = Mage::helper("vendorslivechat")->getLimitDayMessage();
            $messages = Mage::getModel("vendorslivechat/message")->getCollection()->addFieldToFilter("session_id",array('eq'=>$this->getSessionBox()->getId()))->addFieldToFilter('created_time', array('from'  => $date));
            return $messages;
        }
        else{
            return array();
        }
    }
    public function getBoxName(){
        if($this->getSessionBox()) return $this->getSessionBox()->getName();
        return null;
    }

    public function getBoxEmail(){
        if($this->getSessionBox()) return $this->getSessionBox()->getEmail();
        return null;
    }

    public function getBoxIp(){
        if($this->getSessionBox()) return $this->getSessionBox()->getIp();
        return null;
    }

    public function getGeoIp(){
        $ip = $this->getBoxIp();
        $geo= new VES_VendorsLiveChat_Helper_Geoip();
        $geo->setRecord('GeoLiteCity',$ip);
        return $geo;
    }

    public function getClassNameMessageBox($type){
        return Mage::getModel("vendorslivechat/message_type")->getTypeNameVendor($type);
    }

    public function getStatusSessionBox(){
        if($this->getSessionBox()->getData("status") != VES_VendorsLiveChat_Model_Session_Status::STATUS_ACCEPT) {
            return true;
        }
        return false;
    }
}