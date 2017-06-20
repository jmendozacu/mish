<?php

class VES_VendorsLiveChat_Model_Session extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorslivechat/session');
    }

    public function setSession($session_id,$data){
        $command = Mage::getModel("vendorslivechat/command")->setData($data)->setData("session_id",$session_id);
        try{
            $command->save();
        }
        catch (Exception $e) {
        }
        return true;
    }

    public function getSession($session_id){
        $commands = Mage::getModel("vendorslivechat/command")->getCollection()->addFieldToFilter("session_id",$session_id)->getData();
        return array_slice($commands, -3,3,true);
    }


    public function unSession($session_id){
        $commands = Mage::getModel("vendorslivechat/command")->getCollection()->addFieldToFilter("session_id",$session_id);
        foreach($commands as $command){
            $command->delete();
        }
        return;
    }

}