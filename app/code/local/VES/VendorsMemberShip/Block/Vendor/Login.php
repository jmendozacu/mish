<?php
class VES_VendorsMemberShip_Block_Vendor_Login extends Mage_Directory_Block_Data
{
    public function getPostActionUrl(){
        return $this->getUrl('membership/membership/loginPost');
    }
}