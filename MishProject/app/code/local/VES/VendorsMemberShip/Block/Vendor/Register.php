<?php
class VES_VendorsMemberShip_Block_Vendor_Register extends Mage_Directory_Block_Data
{
    public function getFormData(){
        return new Varien_Object(Mage::getSingleton('vendors/session')->getVendorFormData());
    }
}