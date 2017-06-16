<?php

class VES_VendorsLiveChat_Model_Livechat extends Mage_Core_Model_Abstract
{
    private $_sleepTimeLimit	= 1;
    private  $_sleptTime		= 0;

    private $_updateTime = 0;
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorslivechat/livechat');
    }


    public function getCommands($box_session,$type,$vendor_id = null,$command_is_run = null){
        $sleptTime = $this->_sleptTime;
        $sleepTimeLimit = $this->_sleepTimeLimit;
        $update_time = $this->_updateTime;
        $box_ids = array();
        $json = array();
        $conditions = array();
        if($box_session != "" && $box_session != null){
            $boxs = explode(",",$box_session);
            foreach($boxs as $session){
                $tmp = explode("||",$session);
                $conditions[] = $tmp[0];
            }
        }
        $result = Mage::getModel("vendorslivechat/command_process")->getCommand($conditions,$type,$command_is_run);

        if(sizeof($result) != 0){
            $json["has_command"] = true;
            $json["commands"] = $result;
            $json_update = $this->updateTimeCustomer($conditions,$vendor_id);
            $json_message = Mage::getModel("vendorslivechat/message_process")->getMessageNew($box_session);
            $tmp_meg1 = array_merge($json,$json_update);
            $meg = array_merge($tmp_meg1,$json_message);
            echo json_encode($meg);
            exit;
        }
        else{
            $sleptTime += 1;
            $this->setSleptTime($sleptTime);
            if($sleptTime > $sleepTimeLimit){
                $json["has_command"] = false;
                $json_update = $this->updateTimeCustomer($conditions,$vendor_id);
                $json_message = Mage::getModel("vendorslivechat/message_process")->getMessageNew($box_session);
                $tmp_meg1 = array_merge($json,$json_update);
                $meg = array_merge($tmp_meg1,$json_message);
                echo json_encode($meg);
                return;
            }
            $this->getCommands($box_session,$type,$vendor_id,$command_is_run);
        }

    }


    public function  getCommandVendor($box_session,$type,$command_is_run = null){
            $sleptTime = $this->_sleptTime;
            $sleepTimeLimit = $this->_sleepTimeLimit;
            $json = array();

            $vendor = Mage::getSingleton('vendors/session')->getVendor();

            $conditions = array();
            $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
            $time_deadline = Mage::helper("vendorslivechat")->getDeadlineTimeLoadBox();
            $sessions = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$vendor->getId()))->addFieldToFilter("is_closed",0);
            foreach($sessions as $session){
                $time1 = $timestamp - $session->getData("customer_update_time");
                $time2 = $timestamp - $session->getData("vendor_update_time");

                if($time1 <= $time_deadline && $time2 <= $time_deadline){
                     $conditions[] = $session->getId();
                }
            }
            $result = Mage::getModel("vendorslivechat/command_process")->getCommand($conditions,$type,$command_is_run);
            if(sizeof($result) != 0){
                $json["has_command"] = true;
                $json["commands"] = $result;
                $json_message = Mage::getModel("vendorslivechat/message_process")->getMessageNew($box_session);
                $json_update = $this->updatetimeVendor($box_session);
                $tmp_meg1 = array_merge($json,$json_update);
                $meg = array_merge($tmp_meg1,$json_message);
                echo json_encode($meg);
                exit;
            }
            else{
                $sleptTime += 1;
                $this->setSleptTime($sleptTime);
                if($sleptTime > $sleepTimeLimit){
                    $json["has_command"] = false;
                    $json_update = $this->updatetimeVendor($box_session);
                    $json_message = Mage::getModel("vendorslivechat/message_process")->getMessageNew($box_session);
                    $tmp_meg1 = array_merge($json,$json_update);
                    $meg = array_merge($tmp_meg1,$json_message);
                    echo json_encode($meg);
                    return;
                }
                $this->getCommandVendor($box_session,$type,$command_is_run);
            }
    }

    
    public function setSleptTime($time){
        $this->_sleptTime = $time;
    }


    public function updateTimeCustomer($sessions,$vendor_id){
        $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
        $time_update = Mage::helper("vendorslivechat")->getDeadlineTimeUpdate();
        $cmdata = array();
        $result = array();

        $vendor = Mage::getModel("vendors/vendor")->load($vendor_id);
        if($vendor->getId() && $vendor_id!=null){
            $livechat = Mage::getModel("vendorslivechat/livechat")->load($vendor->getId(),"vendor_id");
            $class_status = Mage::getModel("vendorslivechat/box_status")->getStatusClass($livechat->getStatus());
            $title = Mage::getModel("vendorslivechat/box_status")->getTitle($livechat->getStatus(),$vendor->getId());
            
            $result["status_vendor"] = json_encode(array("class"=>$class_status,"vendor_id"=>$livechat->getVendorId(),"session_id"=>null,"title"=>$title));
            
        }
        foreach($sessions as $session_id){
            $session = Mage::getModel("vendorslivechat/session")->load($session_id);

            $livechat = Mage::getModel("vendorslivechat/livechat")->load($session->getVendorId(),"vendor_id");

            $class_status = Mage::getModel("vendorslivechat/box_status")->getStatusClass($livechat->getStatus());
            $title = Mage::getModel("vendorslivechat/box_status")->getTitle($livechat->getStatus(),$session->getVendorId());
            $session->setData("customer_update_time",$timestamp);

            $text = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
            $text .=  '<p>'.Mage::helper("vendorslivechat")->__(" The representative has left the conversation. If you have further questions please leave a message .Thank you! ").'</p>';
            $text .= "</li>";

            $box_data = array("session_id"=>$session->getId(),"class"=>$class_status,"vendor_id"=>$livechat->getVendorId(),"title"=>$title);


            if($vendor_id == $livechat->getVendorId()) unset($result["status_vendor"]);

            if($livechat->getStatus() == VES_VendorsLiveChat_Model_Status::STATUS_OFFLINE){
                $cmdata[] =  array("message"=>$text,"session_id"=>$session->getId());//,"class"=>$class_status,"vendor_id"=>$livechat->getVendorId());
                $session->setData("is_closed",1);
                Mage::getModel("vendorslivechat/session")->unSession($session->getId());
            }
            else{
                if($timestamp - $session->getData("vendor_update_time") >= $time_update && $session->getData("is_closed") == 0){
                    $cmdata[] =  array("message"=>$text,"session_id"=>$session->getId());//"class"=>$class_status,"vendor_id"=>$livechat->getVendorId());
                    $session->setData("is_closed",1);
                    $livechat->setStatus(VES_VendorsLiveChat_Model_Status::STATUS_OFFLINE)->save();
                    Mage::getModel("vendorslivechat/session")->unSession($session->getId());
                }
                else{
                    if($session->getData("is_closed") == 1) {
                        $box_data["message"] = $text;
                    }
                }
            }
            try{
                $session->save();
            }
            catch (Exception $e) {
                $result["update_time"] = false;
            }
            $boxdata[] = $box_data;
        }
        if(sizeof($boxdata) != 0 ) {
            $result["box_data"] = json_encode($boxdata);
        }
        if(sizeof($cmdata) != 0 ) {
            $result["update_time"] = true;
            $result["data"] = json_encode($cmdata);
        }
        return $result;

    }

    public function updatetimeVendor($box_session){
        $conditions = array();
        if($box_session != "" && $box_session != null){
            $boxs = explode(",",$box_session);
            foreach($boxs as $session){
                $tmp = explode("||",$session);
                $conditions[] = $tmp[0];
            }
        }

        $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
        $time_update = Mage::helper("vendorslivechat")->getDeadlineTimeUpdate();
        $cmdata = array();
        $result =  array();
        $boxdata = array();
        foreach($conditions as $session_id){
            $session = Mage::getModel("vendorslivechat/session")->load($session_id);
            $session->setData("vendor_update_time",$timestamp);

            $text = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
            $text .=  '<p><strong>'.$session->getName().'</strong>'.Mage::helper("vendorslivechat")->__(" has left the Chat with you").'</p>';
            $text .= "</li>";
            $box_data = array("session_id"=>$session->getId());
            if($timestamp - $session->getData("customer_update_time") >= $time_update){
                $cmdata[] =  array("message"=>$text,"session_id"=>$session->getId());
                $session->setData("is_closed",1);
                Mage::getModel("vendorslivechat/session")->unSession($session->getId());
            }
            else{
                if($session->getData("is_closed") == 1) {
                    $box_data["message"] = $text;
                }
            }
            try{
                $session->save();
            }
            catch (Exception $e) {
                $result["update_time"] = false;
            }
            $boxdata[] = $box_data;
        }
        if(sizeof($boxdata) != 0 ) {
            $result["box_data"] = json_encode($boxdata);
        }
        if(sizeof($cmdata) != 0 ) {
            $result["update_time"] = true;
            $result["data"] = json_encode($cmdata);
        }
        return $result;
    }
}