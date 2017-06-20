<?php

class VES_VendorsLiveChat_Model_Message_Type extends Varien_Object
{
    const TYPE_CUSTOMER	= 1;
    const TYPE_VENDOR	= 2;
    const TYPE_SYSTEM	= 3;

    static public function getOptionArray()
    {

    }
    public function getTypeName($type){
        if(!$type) throw new Mage_Core_Exception('The type id is not exist');
        $class= "";
        switch($type){
            case self::TYPE_CUSTOMER:
                $class = "self";
                break;
            case self::TYPE_VENDOR:
                $class = "other";
                break;
        }
        return $class;
    }

    public function getTypeNameVendor($type){
        if(!$type) throw new Mage_Core_Exception('The type id is not exist');
        $class= "";
        switch($type){
            case self::TYPE_CUSTOMER:
                $class = "other";
                break;
            case self::TYPE_VENDOR:
                $class = "self";
                break;
        }
        return $class;
    }

    public function getMessageTitle($type,$customer,$vendor){
        if(!$type) throw new Mage_Core_Exception('The type id is not exist');
        $class= "";
        switch($type){
            case self::TYPE_CUSTOMER:
                $class = $customer;
                break;
            case self::TYPE_VENDOR:
                $class = $vendor;
                break;
        }
        return $class;
    }
}