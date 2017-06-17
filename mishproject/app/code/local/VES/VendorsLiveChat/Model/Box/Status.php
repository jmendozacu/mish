<?php

class VES_VendorsLiveChat_Model_Box_Status extends Varien_Object
{
    const STATUS_SHOW	= 1;
    const STATUS_OFFLINE	= 2;
    const STATUS_INVISIBLE	= 3;
    const STATUS_DONOT	= 4;

    public function getOptionArray()
    {
        return array(
            self::STATUS_SHOW    => Mage::helper('vendorslivechat')->__('Online'),
            self::STATUS_OFFLINE   => Mage::helper('vendorslivechat')->__('Offline'),
            self::STATUS_DONOT   => Mage::helper('vendorslivechat')->__('Do not disturb'),
            self::STATUS_INVISIBLE   => Mage::helper('vendorslivechat')->__('Invisible'),
        );
    }

    public function getStatusClass($status){
        $class = null;
        switch($status){
            case VES_VendorsLiveChat_Model_Box_Status::STATUS_DONOT:
                $class="ves-livechat-busy";
                break;
            case VES_VendorsLiveChat_Model_Box_Status::STATUS_SHOW:
                $class="ves-livechat-online";
                break;
            case VES_VendorsLiveChat_Model_Box_Status::STATUS_INVISIBLE:
                $class="ves-livechat-invisible";
                break;
            case VES_VendorsLiveChat_Model_Box_Status::STATUS_OFFLINE:
                $class="ves-livechat-offline";
                break;
            default:
                $class="ves-livechat-offline";
                break;
        }
        return $class;
    }

    public function  getTitle($status,$vendor_id){
        $title = null;
        switch($status){
            case VES_VendorsLiveChat_Model_Box_Status::STATUS_DONOT:
                $title= Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/online',$vendor_id);
                break;
            case VES_VendorsLiveChat_Model_Box_Status::STATUS_SHOW:
                $title=Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/online',$vendor_id);
                break;
            case VES_VendorsLiveChat_Model_Box_Status::STATUS_INVISIBLE:
                $title=Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/offline',$vendor_id);
                break;
            case VES_VendorsLiveChat_Model_Box_Status::STATUS_OFFLINE:
                $title=Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/offline',$vendor_id);
                break;
            default:
                $title=Mage::helper('vendorsconfig')->getVendorConfig('vendorslivechat/minimized/offline',$vendor_id);
                break;
        }
        return $title;
    }
}