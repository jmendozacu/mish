<?php
class VES_VendorsLiveChat_Block_Customer_Before extends Mage_Core_Block_Template
{

    public function getSessionBox(){

        $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
        $time_deadline = Mage::helper("vendorslivechat")->getDeadlineTimeLoadBox();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if($customer->getId()){
            $session_id = $customer->getId();
            $sessions = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("customer_id",array("eq"=>$session_id))->addFieldToFilter("is_closed",array("eq"=>0));
        }
        else{
            $session_id = Mage::helper("vendorslivechat")->getSesssionId();
            $sessions = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("session_key",array("eq"=>$session_id))->addFieldToFilter("is_closed",array("eq"=>0));
        }

        $data = array();
        foreach($sessions as $session){
           // echo "tesst";exit;
            $time1 = $timestamp - $session->getData("customer_update_time");
            $time2 = $timestamp - $session->getData("vendor_update_time");

            if($time1 <= $time_deadline && $time2 <= $time_deadline){
                $data[]  = $session;
            }
            else{
                Mage::getModel("vendorslivechat/session")->unSession($session->getId());
                if($time2<=$time_deadline) {
                    $livechat = Mage::getModel("vendorslivechat/livechat")->load($session->getVendorId(),"vendor_id");
                    $livechat->setStatus(VES_VendorsLiveChat_Model_Status::STATUS_OFFLINE)->save();
                }
            }
        }

        return $data;
    }

    public function getObjectMessageType(){
        $object = array();
        $sessions = $this->getSessionBox();
        foreach($sessions as $session){
            $object[] = $session->getId();
        }
        return json_encode($object);
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

    public function getCommands(){
        $object = array();
        $sessions = $this->getSessionBox();

        foreach($sessions as $session){
            $command_alls =Mage::getModel("vendorslivechat/session")->getSession($session->getId());
            $resull =null;

           // $command_alls = array_slice($command_all, -3,3,true);

            foreach($command_alls as $key=>$commands){
                if($commands["type"] == VES_VendorsLiveChat_Model_Command_Type::TYPE_FRONTEND || $commands["type"] == VES_VendorsLiveChat_Model_Command_Type::TYPE_GLOBAL){
                    $resull .= $commands["command_id"]."|";
                }
            }
            if($resull != null){
                $object[] = $session->getId()."||".trim($resull,"|");
            }
        }
        return json_encode($object);
    }

    public function getDisplayBoxHtml(){
        $sessions = $this->getSessionBox();
        $html = "";
        $i=0;
        foreach($sessions as $session){
                $i++;
                $block = $this->getLayout()->createBlock('vendorslivechat/box')->setTemplate('ves_vendorslivechat/box.phtml')->setSessionId($session->getId())->setNumberSessionTab($i);
                $html .= $block->toHtml();
        }
        return $html;
    }

    public function getVendorId(){
        $vendor_id = null;
        if(Mage::registry("vendor")){
            $vendor_id = Mage::registry("vendor")->getId();
        }
        if(Mage::registry("product") && Mage::registry("product")->getVendorId()){
            $vendor_id = Mage::registry("product")->getVendorId();
        }
        return $vendor_id;
    }

}