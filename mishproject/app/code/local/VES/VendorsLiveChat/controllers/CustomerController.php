<?php
class VES_VendorsLiveChat_CustomerController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch(){
        $this->setFlag("process",Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION,"no-start");
        parent::preDispatch();
    }
    public function processAction()
    {

        $type = $this->getRequest()->getParam("type");
        $type = $type ? $type:'';
        $command = $this->getRequest()->getParam("commands");
        $vendor_id = $this->getRequest()->getParam("vendor");
        $model = $this->getRequest()->getParam("model");
        $box = $this->getRequest()->getParam("box");
        if(isset($command) && $command != null){
            Mage::getModel("vendorslivechat/livechat")->getCommands($box,VES_VendorsLiveChat_Model_Command_Type::TYPE_FRONTEND,$vendor_id,$command);
        }
        else{
            Mage::getModel("vendorslivechat/livechat")->getCommands($box,VES_VendorsLiveChat_Model_Command_Type::TYPE_FRONTEND,$vendor_id);
        }

    }

    public function  controlAction(){
        $command = $this->getRequest()->getParam("command");
        $data = $this->getRequest()->getParam("data");
        $session = $this->getRequest()->getParam("session");
        if($command){
            $type =  Mage::getModel("vendorslivechat/command_type")->getCommandType($command);

     
            if($command == "send_message_customer"){
            	$cmdata = Mage::getModel("vendorslivechat/command")->processData($session,$command,$data);
                echo json_encode(array('success'=>false));
                exit;
            }
            $sessions = Mage::getModel("vendorslivechat/session")->load($session);
            if($sessions->getId()){
            	$cmdata = Mage::getModel("vendorslivechat/command")->processData($sessions,$command,$data);
                $command_datas = array('command'=>$command,"type"=>$type,"data"=>$cmdata);
                Mage::getModel("vendorslivechat/session")->setSession($sessions->getId(),$command_datas);

                echo json_encode(array('success'=>true));
            }
            else{
                echo json_encode(array('success'=>false));
            }
        }else{
            echo json_encode(array('success'=>false));
        }
    }

    public function createboxAction(){
        $time = Mage::helper("vendorslivechat")->getDateNow();
        $id = $this->getRequest()->getParam("vendor_id");
        $name = $this->getRequest()->getParam("name");
        $email = $this->getRequest()->getParam("email");
        $customer_id = $this->getRequest()->getParam("customer_id");
        $message = $this->getRequest()->getParam("question");
        
        $livechat = Mage::getModel("vendorslivechat/livechat")->load($id,"vendor_id");
        if($livechat->getStatus() == VES_VendorsLiveChat_Model_Status::STATUS_INVISIBLE || $livechat->getStatus() == VES_VendorsLiveChat_Model_Status::STATUS_OFFLINE){
            $message = Mage::helper("vendorslivechat")->__("No representative of this vendor is available.  You can also leave a message contact. Thank you!");
            $result = array('success'=>false,"msg"=>$message);
        }
        else{
            $ms_ids = array();
            $commands = null;
            $session_id = Mage::helper("vendorslivechat")->getSesssionId();
            $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
            $ip = Mage::helper("vendorslivechat")->getClientIP();
            $cmd = array(
                'vendor_id'=>$id,
                "name"=>$name,
                "email"=>$email,
                "created_time"=>$time,
                "update_time"=>$time,
                "show_on"=>1,
                "ip"=>$ip,
                "status"=>VES_VendorsLiveChat_Model_Session_Status::STATUS_PENDING,
                "show_on"=>1,
                "created_time"=>$time,
                "update_time"=>$time,
                "customer_update_time"=>$timestamp,
                "vendor_update_time"=>$timestamp
            );
            if($customer_id){
                $sessions = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$id))->addFieldToFilter("customer_id",array("eq"=>$customer_id))->getFirstItem();
                if($sessions->getId()){
                    Mage::getModel("vendorslivechat/session")->unSession($sessions->getId());
                    $date = Mage::helper("vendorslivechat")->getLimitDayMessage();
                    $session = Mage::getModel("vendorslivechat/session")->load($sessions->getId());
                    $session->setData("is_closed",0);
                    $session->setData("show_on_backend",0);
                    $session->setData("customer_update_time",$timestamp);
                    $session->setData("vendor_update_time",$timestamp);
                    $session->setData("status",VES_VendorsLiveChat_Model_Session_Status::STATUS_PENDING);
                    $messages = Mage::getModel("vendorslivechat/message")->getCollection()->addFieldToFilter("session_id",$session->getId())->addFieldToFilter('created_time', array('from'  => $date));
                    foreach($messages as $mes){
                        $ms_ids[] = $mes->getId();
                    }
                }
                else{
                    $session = Mage::getModel("vendorslivechat/session");
                    $cmd["session_key"] = null;
                    $cmd["customer_id"] = $customer_id;
                    $session->setData($cmd);
                }
            }
            else{
                $session_id = Mage::helper("vendorslivechat")->getSesssionId();
                $session = Mage::getModel("vendorslivechat/session");
                $cmd["session_key"] = $session_id;
                $cmd["customer_id"] = null;
                $session->setData($cmd)->setId();
            }
             try{
                 $session->save();
                 $message_id = Mage::getModel("vendorslivechat/message_process")->saveMessage(array("session_id"=>$session->getId(),"message"=>$message,"time"=>$time));
                 Mage::dispatchEvent('ves_livechat_after_create_box',array("session_id"=>$session->getId(),"message"=>$message,"time"=>$time));
                 $block = $this->getLayout()->createBlock('vendorslivechat/box')->setTemplate('ves_vendorslivechat/box.phtml')->setSessionId($session->getId());
                 $string_message_ids = sizeof($ms_ids) != 0 ? $session->getId()."||".implode("|",$ms_ids).'|'.$message_id : $session->getId()."||".$message_id;
                 $result = array('success'=>true,"box"=>$block->toHtml(),"session_id"=>$string_message_ids);
              }
              catch (Exception $e) {
                   $result = array('success'=>false);
              }
        }
        echo json_encode($result);
    }


    public function deleteboxAction(){
        $id = $this->getRequest()->getParam('box_id');
        $box = Mage::getModel("vendorslivechat/session")->load($id);
        if($box->getId()){
            try{
                $box->delete();
                $result = array('success'=>true);
            }
            catch (Exception $e) {
                $result = array('success'=>false);
            }
        }
        else{
            $result = array('success'=>false);
        }
        echo json_encode($result);
    }

    public function updateshowboxAction(){
        $style = $this->getRequest()->getParam('style');
        $id = $this->getRequest()->getParam('box_id');
        $box = Mage::getModel("vendorslivechat/session")->load($id);
        if($box->getId()){
            try{
                $show = $style == "show" ? 1 : 0;
                $box->setData("show_on",$show)->save();
                $result = array('success'=>true);
            }
            catch (Exception $e) {
                $result = array('success'=>false);
            }
        }
        else{
            $result = array('success'=>false);
        }
        echo json_encode($result);
    }

}