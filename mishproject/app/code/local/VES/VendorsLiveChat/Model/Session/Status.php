<?php

class VES_VendorsLiveChat_Model_Session_Status extends Varien_Object
{
    const STATUS_PENDING	= 1;
    const STATUS_ACCEPT	= 2;
    const STATUS_CLOSE	= 3;

    static public function getOptionArray()
    {
    }
}