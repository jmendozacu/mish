<?php

class VES_VendorsLiveChat_Model_Message_Process extends Varien_Object
{
        public function getMessageNew($box){
            $date = Mage::helper("vendorslivechat")->getLimitDayMessage();
            $data = array();
            if($box != "" && $box != null){
                $boxs = explode(",",$box);
                $data["message_box"] = true;
                $cmdata = array();
                foreach($boxs as $session){
                    $tmp = explode("||",$session);
                    $message_ids = explode("|",$tmp[1]);
                    $messages = Mage::getModel("vendorslivechat/message")->getCollection()->addFieldToFilter('created_time', array('from'  => $date))
                    ->addFieldToFilter("message_id",array("nin"=>$message_ids))
                    ->addFieldToFilter("session_id",$tmp[0]);
                    if(sizeof($messages) != 0 ){
                    $tmp_message_ids = null;
                    $message_text = array();
                    foreach($messages as $message){
                        $tmp_message_ids .= $message->getId()."|";
                        $message_text[] = array("type"=>$message->getType(),"content"=>$message->getContent(),"id"=>$message->getId(),"increment_id"=>$message->getData("increment_id"),"name"=>$message->getData("send_by"));
                    }
                    $tmp_message_ids = trim($tmp_message_ids,"|");
                    $cmdata[] = array("session_id"=>$tmp[0],"message_ids"=>$tmp_message_ids,"message_texts"=>json_encode($message_text));
                    }
                }
                $data["message_data"] = json_encode($cmdata);
            }
            else{
                $data["message_box"] = false;
            }
            return $data;
        }

        public function saveMessage($cmdata){
            $message = Mage::getModel("vendorslivechat/message");
            $session = Mage::getModel("vendorslivechat/session")->load($cmdata["session_id"]);
            $data = array(
                "content"=> $cmdata["message"],
                "type"=>VES_VendorsLiveChat_Model_Message_Type::TYPE_CUSTOMER,
                "created_time"=>$cmdata["time"],
                "update_time"=>$cmdata["time"],
                "session_id"=>$cmdata["session_id"],
                "increment_id"=>null,
                'send_by'=>$session->getName()
            );
            $message->setData($data)
                ->setId()
                ->save();
            return $message->getId();
        }
}