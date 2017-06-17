<?php

class VES_VendorsLiveChat_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Is Module Enable
     */
    public function moduleEnable(){
        $result = new Varien_Object(array('module_enable'=>true));
        Mage::dispatchEvent('ves_vendorslivechat_module_enable',array('result'=>$result));
        return $result->getData('module_enable');
    }

    public function getDateNow(){
        $now = strtotime(now());
        $time = date('Y-m-d H:i:s', $now);
        return $time;
    }

    public function getTimestampNow(){
        return Mage::getModel('core/date')->timestamp(time());;
    }
    public function getSesssionId(){
        // session_start();
        // return session_id()
        return  Mage::getSingleton("core/session")->getEncryptedSessionId();
    }

    public function getLimitDayMessage(){
        $now = strtotime("-2 days");
        $time = date('Y-m-d H:i:s', $now);
        return $time;
    }

    public function getStaticBlock(){
        $cms_block = Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/livechat/cms_block', Mage::getSingleton('vendors/session')->getVendorId());
        if(!$cms_block) return null;
        $block = Mage::getModel("vendorscms/block")->load($cms_block,"identifier");
        if($block->getId()){
            $content = $block->getContent();
        }
        else{
            $content = null;
        }
        return $content;
    }

    public function getDeadlineTimeLoadBox(){
        return 300;
    }

    public function getDeadlineTimeUpdate(){
        return 120;
    }

    public function getRunTimeAjax(){
        return 20000;
    }


    public function getClientIP() {

        if (isset($_SERVER)) {

            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
                return $_SERVER["HTTP_X_FORWARDED_FOR"];

            if (isset($_SERVER["HTTP_CLIENT_IP"]))
                return $_SERVER["HTTP_CLIENT_IP"];

            return $_SERVER["REMOTE_ADDR"];
        }

        if (getenv('HTTP_X_FORWARDED_FOR'))
            return getenv('HTTP_X_FORWARDED_FOR');

        if (getenv('HTTP_CLIENT_IP'))
            return getenv('HTTP_CLIENT_IP');

        return getenv('REMOTE_ADDR');
    }

    public function getAudioUrl(){
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."ves_vendors/livechat/audio/sound.wav";
    }

    public function getUrl($url){
        $url_after = null;
        if (Mage::app()->getStore()->isCurrentlySecure()){
            $url_after = Mage::getUrl($url,array('_secure'=>true));
        }
        else{
            $url_after = Mage::getUrl($url);
        }
        return $url_after;
    }
}