<?php
class VES_VendorsLiveChat_Block_Box extends Mage_Core_Block_Template
{
    public function  getSessionBox(){
        $id = $this->getSessionId();
        $session = Mage::getModel("vendorslivechat/session")->load($id);
        return $session;
    }

    public function getVendor(){
        $vendor_id = $this->getSessionBox()->getData("vendor_id");
        $vendor = Mage::getModel("vendors/vendor")->load($vendor_id);
        return $vendor;
    }

    public function getVendorName(){
        return $this->getVendor()->getData("vendor_id");
    }

    public function getDisplayContent(){
        $show = $this->getSessionBox()->getData("show_on");
        if($show == 0) return "display:none";
        else {
            return null;
        }
    }
    public function getBoxShowFrontEnd(){
        return $this->getSessionBox()->getData("show_on");
    }
    public function getMessageBox(){
        $date = Mage::helper("vendorslivechat")->getLimitDayMessage();
        $box_id = $this->getSessionBox()->getId();
        $message = Mage::getModel("vendorslivechat/message")->getCollection()->addFieldToFilter("session_id",array("eq"=>$box_id))->addFieldToFilter('created_time', array('from'  => $date));;
        return $message;
    }

    public function getClassNameMessageBox($type){
         return Mage::getModel("vendorslivechat/message_type")->getTypeName($type);
    }

    public function getStatusSessionBox(){
        if($this->getSessionBox()->getData("status") != VES_VendorsLiveChat_Model_Session_Status::STATUS_ACCEPT) {
            return true;
        }
        if($this->getClassStatus() != "ves-livechat-online" && $this->getClassStatus() != "ves-livechat-busy") return true;
        return false;
    }

    public function getNoteBox($id){
        $session = Mage::getModel("vendorslivechat/session")->load($id);
        $note ="";
        if($session->getStatus() == VES_VendorsLiveChat_Model_Session_Status::STATUS_PENDING){
            $note = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
            $note .= '<p>'.Mage::helper("vendorslivechat")->__("A representative will be connected, please be patient.").'</p>';
            $note  .= "</li>";
        }
        else{
            if($session->getStatus() == VES_VendorsLiveChat_Model_Session_Status::STATUS_CLOSE){
                $note = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
                $note .= '<p>'.Mage::helper("vendorslivechat")->__("Your request could not be served. Please try again later or leave a message .Thank you!").'</p>';
                $note  .= "</li>";
            }
            else{
                $note = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
                $note  .= "</li>";
            }
        }
        return $note;
    }

    public function getFunctionClickBox($id){
        $session = Mage::getModel("vendorslivechat/session")->load($id);
        $function = null;
        if($session->getStatus() == VES_VendorsLiveChat_Model_Session_Status::STATUS_CLOSE){
            $function = "control.deleteBox(".$id.",".$this->getVendor()->getId().")";
        }
        else{
            $function = "ChatOption.showConfirmBox(".$id.")";
        }
        return $function;
    }

    public function getStatusLiveChat(){
        $vendor = $this->getVendor();
        $livechat = Mage::getModel("vendorslivechat/livechat")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$vendor->getId()))->getFirstItem();
        return $livechat->getStatus();
    }

    public function getClassStatus(){
        $status = $this->getStatusLiveChat();

        $class = "ves-livechat-offline";
            //echo "test";exit;
            if($status == VES_VendorsLiveChat_Model_Status::STATUS_ONLINE){
                $class = "ves-livechat-online";
            }
            else{
                if($status == VES_VendorsLiveChat_Model_Status::STATUS_INVISIBLE){
                    $class =  "ves-livechat-invisible";
                }
                else{
                    if($status == VES_VendorsLiveChat_Model_Status::STATUS_DONOT){
                        $class =  "ves-livechat-busy";
                    }
                    else{
                        $class = "ves-livechat-offline";
                    }
                }
            }
        return $class;
    }

    public function getTitle(){
        return Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/chat_windown/title',$this->getVendor()->getId());
    }

    public function getMessageTitle($type){
        return Mage::getModel("vendorslivechat/message_type")->getMessageTitle($type,$this->getSessionBox()->getData("name"),$this->getVendorName());
    }
}