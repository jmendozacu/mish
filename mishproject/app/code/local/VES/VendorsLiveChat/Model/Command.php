<?php

class VES_VendorsLiveChat_Model_Command extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorslivechat/command');
    }
    
    public function getVendorsName(){
    	return $this->getVendors()->getVendorId();
    }
    
    public function getVendors(){
    	return Mage::getSingleton('vendors/session')->getVendor();
    }
    
    public function processData($session,$command,$data){
    	$cmdata = "";
    	$app = Mage::getSingleton('core/layout');
    	$time = Mage::helper("vendorslivechat")->getDateNow();
    	
    	switch($command){
    		case  "decline_box":
    			$status = VES_VendorsLiveChat_Model_Session_Status::STATUS_CLOSE;
    			$session->setData("status",$status)->save();
    			$text_backend = '<p><strong>'.$this->getVendorsName().'</strong>'.Mage::helper("vendorslivechat")->__(" has left the Chat with ")."<strong>".$session->getName().'</strong></p>';
    			$text_frontend = '<p>'.Mage::helper("vendorslivechat")->__("  Your request could not be served. Please try again later or leave a message .Thank you! ").'</p>';
    			$cmdata = array("text_backend"=>$text_backend,"text_frontend"=>$text_frontend);
    			break;
    		case "accept_box":
    			$status = VES_VendorsLiveChat_Model_Session_Status::STATUS_ACCEPT;
    			$session->setData("status",$status)->save();
    			$text_backend = '<p>'.Mage::helper("vendorslivechat")->__(" Chat has been accepted  ").'</p>';
    
    			$text_frontend = '<p><strong>'.$this->getVendorsName().'</strong>'.Mage::helper("vendorslivechat")->__(" enters the chatroom. ").'</p>';
    			$button = '<button class="scalable end" title="End Chat" type="button" onclick="control.endBox('.$session->getId().')" style="">';
    			$button .=  '<span><span>'.Mage::helper("vendorslivechat")->__("End Chat").'</span></span>';
    			$button .=  '</button>';
    			$reply  = '<div class="reply-bottom">';
    			$reply .= '<button class="scalable reply" title="Reply" type="button" onclick="control.replyMessage('.$session->getId().')" style="">';
    			$reply .=  '<span><span>'.Mage::helper("vendorslivechat")->__("Reply").'</span></span>';
    			$reply .=  '</button>';
    			$reply .=  '</div>';
    			$cmdata = array("text_backend"=>$text_backend,"text_frontend"=>$text_frontend,"button"=>$button,"reply"=>$reply);
    			break;
    		case "closed_box_frontend":
    			if($session->getData("is_closed") == 0 && $session->getId()){
    				$text_backend ='<li class="note" id="ves_livechat_message_note_'.$session->getId().'">';
    				$text_backend .= '<p><strong>'.$session->getName().'</strong>'.Mage::helper("vendorslivechat")->__(" has left the Chat with you").'</p>';
    				$text_backend .= '</li>';
    				$cmdata = array("text_backend"=>$text_backend,"check_close"=>true);
    				$session->setData("is_closed",1)->save();
    
    			}
    			break;
    		case "hidden_box":
    			$sessions = Mage::getModel("vendorslivechat/session")->load($session);
                $sessions->setData("show_on_backend",1)->save();
    			break;
    		case "end_box":
    			if($session->getData("is_closed") == 0 && $session->getId()){
    				$session->setData("show_on_backend",1);
    				$session->setData("is_closed",1);
    				$session->save();
    				$text_frontend = '<p><strong>'.$this->getVendorsName().'</strong>'.Mage::helper("vendorslivechat")->__(" has left the Chat with you").'</p>';
    				$text_frontend .= '<p>'.Mage::helper("vendorslivechat")->__(" The representative has left the conversation. If you have further questions please leave a message .Thank you! ").'</p>';
    				$cmdata = array("text_frontend"=>$text_frontend,"check_close"=>true);
    			}
    			break;
    		case "send_message_vendor":
    			$session_id = explode(",",$session);
    			$message =  Mage::getModel("vendorslivechat/message");
    			$array = array(
    					"content"=>$data,
    					"type"=>VES_VendorsLiveChat_Model_Message_Type::TYPE_VENDOR,
    					'created_time'=>$time,
    					'update_time'=>$time,
    					'session_id'=>$session_id[0],
    					'increment_id'=>$session_id[1],
    					'send_by'=>$this->getVendorsName()
    			);
    			$message->setData($array)->setId();
    			try{
    				$message->save();
    			}
    			catch (Exception $e) {
    			}
    			 
    			$cmdata = array("message_id"=>$message->getId());
    			break;
    		case "send_message_customer":
    			$session_id = explode(",",$session);
    			$session = Mage::getModel("vendorslivechat/session")->load($session_id[0]);
    			$message =  Mage::getModel("vendorslivechat/message");
    			$array = array(
    					"content"=>$data,
    					"type"=>VES_VendorsLiveChat_Model_Message_Type::TYPE_CUSTOMER,
    					'created_time'=>$time,
    					'update_time'=>$time,
    					'session_id'=>$session_id[0],
    					'increment_id'=>$session_id[1],
    					'send_by'=>$session->getName()
    			);
    			$message->setData($array)->setId();
    			try{
    				$message->save();
    			}
    			catch (Exception $e) {
    			}
    			$cmdata = array("message_id"=>$message->getId());
    			break;
    		case "msg_type_frontend":
    			$text = '<li id="ves_message_typing_'.$session->getId().'" class="typing">';
    			$text .= "<p>".$session->getName()." is typing ...."."</p>";
    			$text .= "</li>";
    			$cmdata = array("message"=>$text,"check"=>$data);
    			break;
    		case "msg_type_vendor":
    			$text = '<li id="ves_message_typing_'.$session->getId().'"class="typing">';
    			$text .= "<p>".$this->getVendorsName()." is typing ...."."</p>";
    			$text .= "</li>";
    			$cmdata = array("message"=>$text,"check"=>$data);
    			break;
    
    		default:
    			$cmdata = array("value"=>$data);
    			break;
    	}
    	return json_encode(array($cmdata));
    }
}