<?php
class VES_VendorsLiveChat_Block_Profile_Info extends VES_VendorsLiveChat_Block_Bottom_Box {
    public function getCreateBoxUrl(){
        return Mage::helper("vendorslivechat")->getUrl("vendorslivechat/customer/createbox") ;
    }

    public function getSessionBox(){
        if($this->getCustomerId()) return true;
        return false;
    }

}