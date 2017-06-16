<?php

class VES_VendorsLiveChat_Model_Command_Type extends Varien_Object
{
    const TYPE_FRONTEND	= 1;
    const TYPE_VENDOR	= 2;
    const TYPE_GLOBAL	= 3;

    static public function getOptionArray()
    {

    }

    public function getCommandTypeVendor($type){
        $comand_type = "";
        switch($type){
            case "decline_box":
                $comand_type = self::TYPE_GLOBAL;
                break;
            case "accept_box":
                $comand_type = self::TYPE_GLOBAL;
                break;
            case "end_box":
                $comand_type = self::TYPE_GLOBAL;
                break;
            default:
                $comand_type = self::TYPE_FRONTEND;
        }
        return $comand_type;
    }

    public function getCommandType($type){
        $comand_type = "";
        switch($type){
            case "decline_box":
                $comand_type = self::TYPE_GLOBAL;
                break;
            case "accept_box":
                $comand_type = self::TYPE_GLOBAL;
                break;
            case "delete_box":
                $comand_type = self::TYPE_GLOBAL;
                break;
            default:
                $comand_type = self::TYPE_VENDOR;
        }
        return $comand_type;
    }
}