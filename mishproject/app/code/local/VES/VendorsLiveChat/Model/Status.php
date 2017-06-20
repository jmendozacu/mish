<?php

class VES_VendorsLiveChat_Model_Status extends Varien_Object
{
    const STATUS_ONLINE	= 1;
    const STATUS_OFFLINE	= 2;
    const STATUS_INVISIBLE	= 3;
    const STATUS_DONOT	= 4;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ONLINE    => Mage::helper('vendorslivechat')->__('Online'),
            self::STATUS_OFFLINE   => Mage::helper('vendorslivechat')->__('Offline'),
            self::STATUS_DONOT   => Mage::helper('vendorslivechat')->__('Do not disturb'),
            self::STATUS_INVISIBLE   => Mage::helper('vendorslivechat')->__('Invisible'),
        );
    }
    public function getStatusValue($lable){
        $status = null;
        switch($lable){
            case "online";
                $status = self::STATUS_ONLINE;
                break;
            case "offline";
                $status = self::STATUS_OFFLINE;
                break;
            case "invisible";
                $status = self::STATUS_INVISIBLE;
                break;
            case "busy";
                $status = self::STATUS_DONOT;
                break;
        }
        return $status;
    }
}