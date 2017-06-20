<?php
class VES_VendorsLiveChat_Block_Bottom_Box extends Mage_Core_Block_Template
{

    public function getVendor(){
        $vendor = Mage::registry("current_vendor");
        if($vendor) return $vendor;
        return null;
    }

    public function getVendorName(){
        return $this->getVendor()->getData("vendor_id");
    }

    public function getStatusLiveChat(){
        $vendor = $this->getVendor();
        $livechat = Mage::getModel("vendorslivechat/livechat")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$vendor->getId()))->getFirstItem();
        return $livechat->getStatus();
    }

    public function getClassStatus(){
        $status = $this->getStatusLiveChat();
        $class = "ves-livechat-offline";
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

    public function getCustomerId(){
        if($this->getCustomer()){
            return $this->getCustomer()->getId();
        }
        return null;
    }

    public function getCustomer(){
        $customer = Mage::getModel("customer/session")->getCustomer();
        return $customer->getId() ? $customer : null;
    }

    public function getVendorId(){
        $vendor = $this->getVendor();
        return $vendor->getId();
    }

    public function getCustomerEmail(){
        if($this->getCustomer()){
            return $this->getCustomer()->getEmail();
        }
        return null;
    }
    public function getCustomerName(){
        if($this->getCustomer()){
            return $this->getCustomer()->getData("firstname")." ".$this->getCustomer()->getData("lastname");
        }
        return null;
    }

    public function checkBoxInvisible(){
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
        $time_deadline = Mage::helper("vendorslivechat")->getDeadlineTimeLoadBox();
        if($customer->getId()){
            $session = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$this->getVendorId()))->addFieldToFilter("customer_id",array("eq"=>$customer->getId()))->addFieldToFilter("is_closed",array("eq"=>0))->getFirstItem();
            if($session->getId()){
                $time1 = $timestamp - $session->getData("customer_update_time");
                $time2 = $timestamp - $session->getData("vendor_update_time");
                if($time1 >= $time_deadline || $time2 >= $time_deadline){
                    $session->setData("is_closed",1);
                    $session->save();
                }
                else  return false;
            }
        }
        else{
            $session_id = Mage::helper("vendorslivechat")->getSesssionId();
            $session = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$this->getVendorId()))->addFieldToFilter("session_key",array("eq"=>$session_id))->addFieldToFilter("is_closed",array("eq"=>0))->getFirstItem();
            if($session->getId()){
                $time1 = $timestamp - $session->getData("customer_update_time");
                $time2 = $timestamp - $session->getData("vendor_update_time");
                if($time1 >= $time_deadline || $time2 >= $time_deadline){
                    $session->setData("is_closed",1);
                    $session->save();
                }
                else return false;
            }
        }
        return true;
    }
    public function getButtonInfo(){
        $info = array();
        $status = $this->getStatusLiveChat();
        if($status == VES_VendorsLiveChat_Model_Status::STATUS_ONLINE){
            $info['link'] = "ChatOption.createBoxOnline()";
            $info['icon'] = "icon-online";
            $info["title"] = Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/online',$this->getVendor()->getId());
        }
        else{
            if($status == VES_VendorsLiveChat_Model_Status::STATUS_INVISIBLE){
                $info['link'] = "ChatOption.createBoxOffline()";
                $info['icon'] = "icon-offline";
                $info["title"] = Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/offline',$this->getVendor()->getId());
            }
            else{
                if($status == VES_VendorsLiveChat_Model_Status::STATUS_DONOT){
                    $info['link'] = "ChatOption.createBoxOnline()";
                    $info['icon'] = "icon-online";
                    $info["title"] = Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/online',$this->getVendor()->getId());
                }
                else{
                    $info['link'] = "ChatOption.createBoxOffline()";
                    $info['icon'] = "icon-offline";
                    $info["title"] = Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/offline',$this->getVendor()->getId());
                }
            }
        }
        return $info;
    }

    public function isEnable(){
        $modules = Mage::getConfig()->getNode('modules')->asArray();
        if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
            if($vendor = Mage::registry("vendor")){
                $groupId = $vendor->getGroupId();
                $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('livechat/enabled',$groupId);
                return $subAccountEnableConfig;
            }
        }
        return true;
    }

    public function getTitle(){
        return Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/chat_windown/title',$this->getVendor()->getId());
    }

}