<?php

class VES_VendorsLiveChat_Model_Observer
{
    /*
    public function ves_vendorslivechat_prepare(Varien_Event_Observer $observer){
        $profileBlock = $observer->getProfileBlock();
        $ratingBlock = $profileBlock->getLayout()->createBlock('vendorslivechat/profile_sidebar','vendor.chat')->setTemplate('ves_vendorslivechat/profile/sidebar.phtml');
        $footerProfile = $profileBlock->getChild('footer_profile');
        $footerProfile->insert($ratingBlock, '', false, 'vendors_livechat_block');
    }
    */
    public function ves_vendor_login(Varien_Event_Observer $observer){
        $session = Mage::getSingleton('vendors/session');
        $vendor = $observer->getVendor();
      //  echo "test";exit;
        $result = new Varien_Object(array('vendors_livechat_login_check'=>true));
        Mage::dispatchEvent('vendors_livechat_login',array('check'=>$result,"vendor"=>$vendor));
        if($result->getData("vendors_livechat_login_check")){
            $livechat = Mage::getModel('vendorslivechat/livechat')->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$vendor->getId()))->getFirstItem();
            if($livechat->getId()){
                $model = Mage::getModel('vendorslivechat/livechat')->load($livechat->getId());
                $model->setData("status",VES_VendorsLiveChat_Model_Status::STATUS_ONLINE)->save();
            }
            else{
                $model = Mage::getModel('vendorslivechat/livechat');
                $model->setData("status",VES_VendorsLiveChat_Model_Status::STATUS_ONLINE)->setData("vendor_id",$vendor->getId())->setId()->save();
            }

            if($session->getBeforeAuthUrl() == Mage::getBaseUrl()."vendors/livechat_box/process/?isAjax=true"){
                $session->setBeforeAuthUrl(Mage::helper('vendors')->getDashboardUrl());
            }
        }
    }

    public function ves_vendor_logout(Varien_Event_Observer $observer){
        $vendor = $observer->getVendor();
        $result = new Varien_Object(array('vendors_livechat_logout_check'=>true));
        Mage::dispatchEvent('vendors_livechat_logout',array('check'=>$result,"vendor"=>$vendor));
        if($result->getData("vendors_livechat_logout_check")) {

            $model = Mage::getModel('vendorslivechat/livechat')->load($vendor->getId(), "vendor_id");

            if ($model->getId()) {
                $model->setData("status", VES_VendorsLiveChat_Model_Status::STATUS_OFFLINE)->save();
            }


        }
    }

    public function deleteSessionChat(){
        $vendors = Mage::getSingleton('vendors/vendor')->getCollection();
        $timestamp = Mage::helper("vendorslivechat")->getTimestampNow();
        $time_deadline = Mage::helper("vendorslivechat")->getDeadlineTimeLoadBox();
        foreach($vendors as $vendor){
                $sessions = Mage::getModel("vendorslivechat/session")->getCollection()->addFieldToFilter("vendor_id",array("eq"=>$vendor->getId()));
                foreach($sessions as $session){
                    if($session->getData("is_closed") == 1){
                        Mage::getModel("vendorslivechat/session")->unSession($session->getId());
                    }
                    else{
                        $time1 = $timestamp - $session->getData("customer_update_time");
                        $time2 = $timestamp - $session->getData("vendor_update_time");
                        if($time1 <= $time_deadline || $time2 <= $time_deadline){
                            $session->setData("is_closed",1);
                            $session->save();
                            Mage::getModel("vendorslivechat/session")->unSession($session->getId());
                        }
                    }
                }
        }
    }


    /**
     *
     * Hide the menu if the module is not enabled
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer){
        $resource 	= $observer->getResource();
        $result 	= $observer->getResult();

        if($resource == 'vendors/vendorslivechat' && !Mage::helper('vendorslivechat')->moduleEnable()){
            $result->setIsAllowed(false);
        }
    }

    /**
     * Check if this feature is enabled for the current vendor (Advanced Group plugin is required)
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendorslivechat_module_enable(Varien_Event_Observer $observer){
        $modules = Mage::getConfig()->getNode('modules')->asArray();
        if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
            $result = $observer->getEvent()->getResult();
            if($vendor = Mage::getSingleton('vendors/session')->getVendor()){
                $groupId = $vendor->getGroupId();
                $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('livechat/enabled',$groupId);
                $result->setData('module_enable',$subAccountEnableConfig);
                return;
            }
        }
    }
}