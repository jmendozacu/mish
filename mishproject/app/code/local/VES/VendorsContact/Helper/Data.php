<?php

class VES_VendorsContact_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getUserName()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim($customer->getName());
    }


    public function getEmailTemplate(){
        return Mage::getStoreConfig('vendors/vendors_contact/email_template');
    }

    public function getUserEmail()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }
    public function isEnabled(){
         $isn = Mage::helper('vendorsconfig')->getVendorConfig('vendorscontact/contact/enable',Mage::registry('current_vendor')->getId());
         return $isn == VES_VendorsContact_Model_System_Config_Source_Yesno::CONFIG_YES ? true : false;
    }

    public function getEmailVendor(){
         return Mage::helper('vendorsconfig')->getVendorConfig('vendorscontact/contact/email',Mage::registry('current_vendor')->getId());
    }
    public function getEmailTo(){
        return Mage::getStoreConfig('vendors/vendors_contact/sender_email_identity');
    }

}