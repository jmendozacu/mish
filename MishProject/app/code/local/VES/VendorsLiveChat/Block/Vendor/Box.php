<?php
/**
 * Created by PhpStorm.
 * User: namnh_000
 * Date: 4/15/14
 * Time: 12:09 PM
 */

class VES_VendorsLiveChat_Block_Vendor_Box extends Mage_Adminhtml_Block_Abstract
{

    public function getVendors(){
        return Mage::getSingleton('vendors/session')->getVendor();
    }

    public function getVendorsName(){
    	return $this->getVendors()->getVendorId();
    }
    
    public function getSessionBox(){
        $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
        $time_deadline = Mage::helper("vendorslivechat")->getDeadlineTimeLoadBox();
        $sessions = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$this->getVendors()->getId()))->addFieldToFilter("is_closed",0);
        $session_data = array();
        foreach($sessions as $session){
            if($timestamp - $session->getData("customer_update_time") <= $time_deadline){

                $session_data[] = $session;
            }
            else{
                Mage::getModel("vendorslivechat/session")->unSession($session->getId());
            }
        }

        return $session_data;
    }

    public function getMessages($id){
        $date = Mage::helper("vendorslivechat")->getLimitDayMessage();
        $messages =  Mage::getModel("vendorslivechat/message")->getCollection()->addFieldToFilter("session_id",array("eq"=>$id))->addFieldToFilter('created_time', array('from'  => $date));;
        return $messages;
    }

    public function getClassNameMessageBox($type){
        return Mage::getModel("vendorslivechat/message_type")->getTypeNameVendor($type);
    }


    public function getGeoIp($ip){

        $geo= new VES_VendorsLiveChat_Helper_Geoip();
        $geo->setRecord('GeoLiteCity',$$ip);
        return $geo;
    }

    public function getStatusChat(){
        $image_src = "";
        $lable= "";
        $id = $this->getVendors()->getId();
        $livechat = Mage::getModel("vendorslivechat/livechat")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$id))->getFirstItem();
        switch($livechat->getStatus()){
            case VES_VendorsLiveChat_Model_Status::STATUS_ONLINE:
                $image_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."ves_vendors/livechat/status/online_box.png";
                $lable = $this->__("Online");
                break;
            case VES_VendorsLiveChat_Model_Status::STATUS_INVISIBLE:
                $image_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."ves_vendors/livechat/status/invisible_box.png";
                $lable = $this->__("Invisible");
                break;
            case VES_VendorsLiveChat_Model_Status::STATUS_DONOT:
                $image_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."ves_vendors/livechat/status/busy_box.png";
                $lable = $this->__("Busy");
                break;
            case VES_VendorsLiveChat_Model_Status::STATUS_OFFLINE:
                $image_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."ves_vendors/livechat/status/offline_box.png";
                $lable = $this->__("Offline");
                break;
        }
        return "<img src='".$image_src."' />".$lable;
    }

    public function getNoteBox($id){
        $session = Mage::getModel("vendorslivechat/session")->load($id);
        $note ="";
        if($session->getStatus() == VES_VendorsLiveChat_Model_Session_Status::STATUS_PENDING){
               $note = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
               $note .= "<p><strong>".$session->getName()."</strong>".Mage::helper("vendorslivechat")->__(" is now in Chat with you.")."</p>";
               $note  .= "</li>";
        }
        else{
            if($session->getStatus() == VES_VendorsLiveChat_Model_Session_Status::STATUS_CLOSE){
                $note = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
                $note .= '<p><strong>'.$this->getVendors()->getVendorId().'</strong>'.Mage::helper("vendorslivechat")->__(" has left the Chat with ")."<strong>".$session->getName().'</strong></p>';
                $note  .= "</li>";
            }
            else{
                $note = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
                $note  .= "</li>";
            }
        }
        return $note;
    }

    public function getStatusOption(){
        return Mage::getModel("vendorslivechat/box_status")->getOptionArray();
    }



}