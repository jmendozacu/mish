<?php
class VES_VendorsLiveChat_Block_Catalog_Product_List_Info extends VES_VendorsLiveChat_Block_Catalog_Product_List {
    public function getCreateBoxUrl(){
        return Mage::helper("vendorslivechat")->getUrl("vendorslivechat/customer/createbox") ;
    }

    public function getSessionBox(){
        if($this->getCustomerId()) return true;
        return false;
    }

}