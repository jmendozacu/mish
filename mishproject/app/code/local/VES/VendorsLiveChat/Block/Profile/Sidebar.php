<?php
class VES_VendorsLiveChat_Block_Profile_Sidebar extends Mage_Core_Block_Template {
	protected $_map_collection;

	public function getVendorInfo() {
		return Mage::getModel('vendors/vendor')->loadByVendorId(Mage::registry('vendor_id'));
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
            $onclick = "ChatOption.createBoxOnline()";
        }
        else{
            if($status == VES_VendorsLiveChat_Model_Status::STATUS_INVISIBLE){
                 $onclick = "ChatOption.createBoxOffline()";
            }
            else{
                if($status == VES_VendorsLiveChat_Model_Status::STATUS_DONOT){
                    $onclick = "ChatOption.createBoxOnline()";
                }
                else{
                    $onclick = "ChatOption.createBoxOffline()";
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
    public function getClassStatus(){
        $status = $this->getStatusLiveChat();
        return Mage::getModel("vendorslivechat/box_status")->getStatusClass($status);
    }

	
	/**
	 * Get current vendor
	 * @return VES_Vendors_Model_Vendor
	 */
	public function getVendor(){
		return Mage::registry('vendor');
	}
	
	/**
	 * Get current vendor id
	 * @return int
	 */
	public function getVendorId() {
		return $this->getVendor()->getId();
	}
}