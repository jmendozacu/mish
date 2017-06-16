<?php
class VES_VendorsLiveChat_Block_Catalog_Product_Button extends Mage_Core_Block_Template {
    
     public function checkProductIsVendor(){
         $product = $this->getProduct();
         if(!$product->getVendorId() || !$this->isEnable() || Mage::registry("vendor_id")) return false;
         return true;
     }
     
     public function getVendor(){
         $product = $this->getProduct();
         $vendor = Mage::getModel("vendors/vendor")->load($product->getVendorId());
         return $vendor;
     }
     
     public function getVendorId(){
         return $this->getVendor()->getId();
     }
     
     
     public function isEnable(){
         $modules = Mage::getConfig()->getNode('modules')->asArray();
         if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
              if($vendor = $this->getVendor()){
                 $groupId = $vendor->getGroupId();
                 $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('livechat/enabled',$groupId);
                 return $subAccountEnableConfig;
             }
         }
         return true;
     }
     
     public function getOnClickLink(){
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
                 else  return null;
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
                 else return null;
             }
         }
         $status = $this->getStatusLiveChat();
         if($status == VES_VendorsLiveChat_Model_Status::STATUS_ONLINE){
             $onclick = "ChatOption.createBoxOnline('".$this->getVendorId()."','".$this->getTitle()."')";
         }
         else{
             if($status == VES_VendorsLiveChat_Model_Status::STATUS_INVISIBLE){
                 $onclick = "ChatOption.createBoxOffline('".$this->getVendorId()."','".$this->getTitle()."')";
             }
             else{
                 if($status == VES_VendorsLiveChat_Model_Status::STATUS_DONOT){
                     $onclick = "ChatOption.createBoxOnline('".$this->getVendorId()."','".$this->getTitle()."')";
                 }
                 else{
                     $onclick = "ChatOption.createBoxOffline('".$this->getVendorId()."','".$this->getTitle()."')";
                 }
             }
         }
         return $onclick;
     }
     
     public function getStatusLiveChat(){
         $vendor = $this->getVendor();
         $livechat = Mage::getModel("vendorslivechat/livechat")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$vendor->getId()))->getFirstItem();
         return $livechat->getStatus();
     }
     
     

     public function getTitle(){
         return addslashes(Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/chat_windown/title',$this->getVendor()->getId()))." ( ".$this->getVendor()->getVendorId()." )";
     }
}