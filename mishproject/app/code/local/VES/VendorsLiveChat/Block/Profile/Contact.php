<?php
class VES_VendorsLiveChat_Block_Profile_Contact extends VES_VendorsLiveChat_Block_Bottom_Box {
        public function getContactUrl(){
            return Mage::helper("vendorslivechat")->getUrl("vendorslivechat/contact/save") ;
        }
        public function getVendorId(){
            $vendor = $this->getVendor();
            return $vendor->getId();
        }
}