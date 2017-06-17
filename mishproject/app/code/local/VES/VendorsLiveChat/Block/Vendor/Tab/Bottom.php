<?php
/**
 * Created by PhpStorm.
 * User: namnh_000
 * Date: 4/15/14
 * Time: 12:09 PM
 */

class VES_VendorsLiveChat_Block_Vendor_Tab_Bottom extends VES_VendorsLiveChat_Block_Vendor_Tab_Abstract
{
    public function getBoxShowBackEnd(){
        if($this->getSessionBox()) return $this->getSessionBox()->getData("show_on_backend");
        return null;
    }

    public function getDisplayContent(){
        $show = $this->getSessionBox()->getData("show_on_backend");
        if($show == 1) return "display:none";
        else {
            return null;
        }
    }

    public function getClassNameMessageBox($type){
        return Mage::getModel("vendorslivechat/message_type")->getTypeNameVendor($type);
    }
    
    public function getVendorsName(){
    	return $this->getVendors()->getVendorId();
    }
    
    public function getStatusSessionBox(){
        if($this->getSessionBox()->getData("status") != VES_VendorsLiveChat_Model_Session_Status::STATUS_ACCEPT) {
            return true;
        }
        $vendor = $this->getVendors();
        $livechat = Mage::getModel("vendorslivechat/livechat")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$vendor->getId()))->getFirstItem();
        if($livechat->getStatus() != VES_VendorsLiveChat_Model_Status::STATUS_ONLINE && $livechat->getStatus() != VES_VendorsLiveChat_Model_Status::STATUS_DONOT){
            return true;
        }
        //if($this->getClassStatus() != "ves-livechat-online" && $this->getClassStatus() != "ves-livechat-busy") return true;
        return false;
    }

    public function getNoteBox($id){
        $session = Mage::getModel("vendorslivechat/session")->load($id);
        $note ="";
        if($session->getStatus() == VES_VendorsLiveChat_Model_Session_Status::STATUS_PENDING){
            $note = '<li id="ves_livechat_message_button_'.$session->getId().'" class="button">';
            $note .= '<button class="scalable save" title="Start Chat" type="button" onclick="control.acceptBox(\''.$this->getSessionId().'\',true)" style="">';
            $note .= '<span>';
            $note .=      '<span>'.Mage::helper("vendorslivechat")->__("Start Chat").'</span>';
            $note .=     '</span>';
            $note .=   '</button>';
            $note .=     '<button class="scalable delete" title="Decline" type="button" onclick="control.declineBox(\''.$this->getSessionId().'\',true)" style="">';
            $note .=     '<span>';
            $note .=     '<span>'.Mage::helper("vendorslivechat")->__("Decline").'</span>' ;
            $note .=      '</span>';
            $note .=   '</button>';
            $note  .= "</li>";
        }
        else{
            if($session->getStatus() == VES_VendorsLiveChat_Model_Session_Status::STATUS_CLOSE){
                $note = '<li id="ves_livechat_message_note_'.$session->getId().'" class="note">';
                $note .= '<p><strong>'.$this->getVendors()->getVendorId().'</strong>'.Mage::helper("vendorslivechat")->__(" has left the Chat with ")."<strong>".$session->getName().'</strong></p>';
                $note  .= "</li>";
            }
        }
        return $note;
    }

    public function getFunctionClickBox($id){
        $session = Mage::getModel("vendorslivechat/session")->load($id);
        $function = null;
        if($session->getStatus() == VES_VendorsLiveChat_Model_Session_Status::STATUS_CLOSE){
            $function = "control.deleteBox(".$id.")";
        }
        else{
            $function = "ChatOption.showConfirmBox(".$id.")";
        }
        return $function;
    }
}