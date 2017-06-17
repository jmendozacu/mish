<?php

class VES_VendorsLiveChat_Model_Command_Process extends Varien_Object
{
    public function getCommand($session_ids ,$type, $command_runs=null){
        $result = array();
        if($command_runs != null){
            $command_runs = explode(",",$command_runs);
            $session_runs =  array();
            foreach($session_ids as $session_id){
                  if($session_id == null) continue;
                  foreach($command_runs as $command_run){
                        $command_run_data = explode("||",$command_run);
                        if($session_id == $command_run_data[0]){
                            $command_run_key = explode("|",$command_run_data[1]);
                            $command_all =Mage::getModel("vendorslivechat/session")->getSession($session_id);
                            foreach($command_all as $key=>$commands){
                               if(!in_array((string)$commands["command_id"],$command_run_key)) {
                                    if($commands["type"] == $type || $commands["type"] == VES_VendorsLiveChat_Model_Command_Type::TYPE_GLOBAL){
                                        $command_id = $commands["command_id"];
                                        unset($commands["command_id"]);unset($commands["session_id"]);unset($commands["type"]);
                                        $result[] = $session_id."||".$command_id."||".json_encode($commands);
                                    }
                               }
                            }
                            array_push($session_runs,$session_id);
                        }
                  }
            }
            $session_news = array_diff($session_ids, $session_runs);
            foreach($session_news as $session_id){
                if($session_id == null) continue;
                $command_all =Mage::getModel("vendorslivechat/session")->getSession($session_id);
                foreach($command_all as $key=>$commands){
                    if($commands["type"] == $type || $commands["type"] == VES_VendorsLiveChat_Model_Command_Type::TYPE_GLOBAL){
                        $command_id = $commands["command_id"];
                        unset($commands["command_id"]);unset($commands["session_id"]);unset($commands["type"]);
                        $result[] = $session_id."||".$command_id."||".json_encode($commands);
                    }
                }
            }
        }
        else{
            foreach($session_ids as $session_id){
                if($session_id == null) continue;
                $command_all =Mage::getModel("vendorslivechat/session")->getSession($session_id);
                foreach($command_all as $key=>$commands){
                    if($commands["type"] == $type || $commands["type"] == VES_VendorsLiveChat_Model_Command_Type::TYPE_GLOBAL){
                        $command_id = $commands["command_id"];
                        unset($commands["command_id"]);unset($commands["session_id"]);unset($commands["type"]);
                        $result[] = $session_id."||".$command_id."||".json_encode($commands);
                    }
                }
            }
        }
        return $result;
    }
}