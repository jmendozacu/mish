<?php
class VES_VendorsLiveChat_Block_Vendor_Before extends Mage_Adminhtml_Block_Abstract
{
    public function checkEnableLiveChatGroupVendor(){
        if(!Mage::helper('vendorslivechat')->moduleEnable()){
           return false;
        }
        return true;
    }

    public function getSessionBox(){
        $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
        $time_deadline = Mage::helper("vendorslivechat")->getDeadlineTimeLoadBox();
        $vendor = Mage::getSingleton('vendors/session')->getVendor()->getId();

        $sessions = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$vendor))->addFieldToFilter("is_closed",0);
        $data = array();
        foreach($sessions as $session){
            $time1 = $timestamp - $session->getData("customer_update_time");
            $time2 = $timestamp - $session->getData("vendor_update_time");

            if($time1 <= $time_deadline && $time2 <= $time_deadline){
                $data[]  = $session;
            }
            else{
                Mage::getModel("vendorslivechat/session")->unSession($session->getId());
            }
        }
        return $data;
    }


    public function getMessageBox(){
        $object = array();
        $ms = array();
        $sessions = $this->getSessionBox();

        $date = Mage::helper("vendorslivechat")->getLimitDayMessage();

        foreach($sessions as $session){

            $messages = Mage::getModel("vendorslivechat/message")->getCollection()->addFieldToFilter("session_id",$session->getId())->addFieldToFilter('created_time', array('from'  => $date));
            foreach($messages as $mes){
                $ms[] = $mes->getId();
            }
            $object[] = $session->getId()."||".implode("|",$ms);
        }
        return json_encode($object);
    }

    public function getObjectMessageType(){
        $object = array();
        $sessions = $this->getSessionBox();
        foreach($sessions as $session){
            $object[] = $session->getId();
        }
        return json_encode($object);
    }

    public function getDisplayBoxHtml(){
        $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
        $time_deadline = Mage::helper("vendorslivechat")->getDeadlineTimeLoadBox();
        $sessions = $this->getSessionBox();
        $html = "";
        $i=0;
        foreach($sessions as $session){
        	    $i++;
                $block = $this->getLayout()->createBlock('vendorslivechat/vendor_tab_bottom')->setTemplate('ves_vendorslivechat/chat/box.phtml')->setSessionId($session->getId())->setNumberSessionTab($i);
                $html .= $block->toHtml();
        }
        return $html;
    }

    public function getCommands(){
        $object = array();
        $sessions = $this->getSessionBox();
        foreach($sessions as $session){
            $command_alls =Mage::getModel("vendorslivechat/session")->getSession($session->getId());
            $resull =null;
           // $command_all = array_slice($command_all, -3,3,true);
            foreach($command_alls as $key=>$commands){
                if($commands["type"] == VES_VendorsLiveChat_Model_Command_Type::TYPE_VENDOR || $commands["type"] == VES_VendorsLiveChat_Model_Command_Type::TYPE_GLOBAL){
                    $resull .= $commands["command_id"]."|";
                }
            }
            if($resull != null){
                $object[] = $session->getId()."||".trim($resull,"|");
            }
        }
        return json_encode($object);
    }
}