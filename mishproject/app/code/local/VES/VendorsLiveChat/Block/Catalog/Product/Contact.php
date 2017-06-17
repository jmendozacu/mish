<?php
class VES_VendorsLiveChat_Block_Catalog_Product_Contact extends VES_VendorsLiveChat_Block_Catalog_Product {
        public function getContactUrl(){
            return Mage::helper("vendorslivechat")->getUrl("vendorslivechat/contact/save") ;
        }
        public function getVendorId(){
            $vendor = $this->getVendor();
            return $vendor->getId();
        }
}