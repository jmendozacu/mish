<?php

class VES_VendorsRma_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * Get config system allow to request RMA after order completion
     * @return days
     */

    public function orderExpiryDay(){
        return Mage::getStoreConfig('vendorsrma/general/order_expiry_day');
    }
    
    /**
     * Get config system Time expiry for state "Awaiting Other Party's Response"
     * @return days
     */
    
    public function timeStateExpiry(){
        return Mage::getStoreConfig('vendorsrma/general/max_time_wait_sate');
    }
    

    /**
     *
     * Get config system allow guests to request RMA
     * @return true
     */

    public function allowGuestsRequest(){
        return Mage::getStoreConfig('vendorsrma/general/allow_guests_request') == 0 ? false : true ;
    }

    /**
     *
     * Get config system enable "Print label" option
     * @return true
     */

    public function allowPrint(){
        return Mage::getStoreConfig('vendorsrma/general/allow_print') == 0 ? false : true ;
    }

    /**
     *
     * Get config system enable "Reasons" option
     * @return true
     */

    public function enableReasons(){
        return Mage::getStoreConfig('vendorsrma/general/enable_reasons') == 0 ? false : true ;
    }


    /**
     *
     * Get config system allow "Other" option for reasons
     * @return true
     */

    public function allowOtherOptionReason(){
        return Mage::getStoreConfig('vendorsrma/general/allow_other_reasons') == 0 ? false : true ;
    }



    /** check enabled */

    public function allowReasons(){
        $a = $this->enableReasons();
        $b = $this->allowOtherOptionReason();
        if(!$a && !$b) return false;
        return true ;
    }


    /**
     *
     * Get config system allow send cost refund RMA
     * @return true
     */

    public function allowSendRefund(){
        return Mage::getStoreConfig('vendorsrma/general/allow_send_refund') == 0 ? false : true ;
    }



    /**
     *
     * Get config system allow per-order item RMA
     * @return true
     */

    public function allowPerOrder(){
        return Mage::getStoreConfig('vendorsrma/general/allow_per_order') == 0 ? false : true ;
    }



    /**
     *
     * Get config system "Confirm Shipping" is required
     * @return true
     */

    public function confirmShipping(){
        return Mage::getStoreConfig('vendorsrma/general/confirm_shipping') == 0 ? false : true ;
    }

    /**
     *
     * Get config system Confirm Shipping popup text
     * @return true
     */

    public function confirmShippingText(){
        return Mage::getStoreConfig('vendorsrma/general/confirm_shipping_text') ? Mage::getStoreConfig('vendorsrma/general/confirm_shipping_text') : $this->__("Are you sure !");
    }

    /**
     *
     * Get config system Forbidden filename extensions for uploading
     * @return true
     */

    public function fileExtension(){
        return Mage::getStoreConfig('vendorsrma/general/allow_file_extension');
    }

    /**
     *
     * Get config system Max attachment size, kb
     * @return true
     */

    public function maxSizeUpload(){
        return Mage::getStoreConfig('vendorsrma/general/max_size_upload');
    }


    /**
     *
     * Get config system Show policy
     * @return true
     */

    public function showPolicy(){
        return Mage::getStoreConfig('vendorsrma/policy/enable') == 0 ? false : true ;
    }

    /**
     *
     * Get config system Policy block
     * @return true
     */

    public function policyBlock(){
        return Mage::getStoreConfig('vendorsrma/policy/block');
    }


    /**
     *
     * Get config system Enable email notifications
     * @return true
     */

    public function emailNotify($type){
		
		$config = Mage::getStoreConfig('vendorsrma/contact/enable');
		
		if($type == "mark-customer" || $type == "mark-vendor") return true;
		
		if($config  == VES_VendorsRma_Model_System_Config_Source_Notify::CONFIG_ALL) return true;
		else{
			if($config  == VES_VendorsRma_Model_System_Config_Source_Notify::CONFIG_DISABLE) return false;
			else{
				if($config  == VES_VendorsRma_Model_System_Config_Source_Notify::CONFIG_VENDOR){
					return $type == "vendor" ? true : false;
				}
				
				if($config  == VES_VendorsRma_Model_System_Config_Source_Notify::CONFIG_CUSTOMER){
					return $type == "customer" ? true : false;
				}
				
				if($config  == VES_VendorsRma_Model_System_Config_Source_Notify::CONFIG_ADMIN){
					return $type == "admin" ? true : false;
				}
				
			}
		}
		return true;
    }


    /**
     *
     * Get config system RMA department display name
     * @return true
     */

    public function getContactName(){
    	return Mage::getStoreConfig('vendorsrma/contact/name') ?   Mage::getStoreConfig('vendorsrma/contact/name') : $this->__("Customer Service");;
    }

    /**
     *
     * Get config system RMA department display email
     * @return true
     */

    public function getContactEmail(){
    	if(Mage::getStoreConfig('vendorsrma/contact/email')) return Mage::getStoreConfig('vendorsrma/contact/email');
        return Mage::getStoreConfig('contacts/email/recipient_email');
    }

    /**
     *
     * Get config system RMA department display address
     * @return true
     */

    public function getContactAddress(){
        return Mage::getStoreConfig('vendorsrma/contact/address');
    }


    /**
     *
     * Get config system Email sender
     * @return true
     */

    public function getEmailSender(){
        return Mage::getStoreConfig('vendorsrma/email/sender');
    }


    /**
     *
     * Get config system Base template for customer
     * @return true
     */

    public function getBaseTemplateCustomer(){
        return Mage::getStoreConfig('vendorsrma/email/base_template_customer');
    }

    /**
     *
     * Get config system Base template for admin
     * @return true
     */

    public function getBaseTemplateAdmin(){
        return Mage::getStoreConfig('vendorsrma/email/base_template_admin');
    }


    /**
     *
     * Get config system Base template for vendor
     * @return true
     */

    public function getBaseTemplateVendor(){
        return Mage::getStoreConfig('vendorsrma/email/base_template_vendor');
    }

    /**
     *
     * Get config system RMA department display name
     * @return true
     */

    public function getContactNameVendor($vendor){
        $name = Mage::helper('vendorsconfig')->getVendorConfig('vendorsrma/contact/name', $vendor->getId());
        $return = $name ? $name : $vendor->getVendorId();
        return $return;
    }

    /**
     *
     * Get config system RMA department display email
     * @return true
     */

    public function getContactEmailVendor($vendor){
        $name = Mage::helper('vendorsconfig')->getVendorConfig('vendorsrma/contact/email', $vendor->getId());
        $return = $name ? $name : $vendor->getEmail();
        return $return;
    }

    /**
     *
     * Get config system RMA department display address
     * @return true
     */

    public function getContactAddressVendor($vendor){
        $name = Mage::helper('vendorsconfig')->getVendorConfig('vendorsrma/contact/address', $vendor->getId());
        $return = $name ? $name : $vendor->getAddress();
        return $return;
    }


    /**
     *
     * Get config system RMA department display name
     * @return true
     */

    public function getContactNameAdmin(){
        $user = Mage::getSingleton('admin/session');
        $name =  Mage::getStoreConfig('vendorsrma/contact/name');
        
        $customName = $name ? $name : $user->getUser()->getUsername();
        
        $customName = $customName ? $customName : $this->__("Customer Service");
        
        return  $customName;
    }

    /**
     *
     * Get config system RMA department display email
     * @return true
     */

    public function getContactEmailAdmin(){
        $user = Mage::getSingleton('admin/session');
        $email =  Mage::getStoreConfig('vendorsrma/contact/email');;
        
        return $email ? $email : $user->getUser()->getEmail();
    }

    /**
     *
     * Get config system RMA department display address
     * @return true
     */

    public function getContactAddressAdmin(){
        $name =  Mage::getStoreConfig('vendorsrma/contact/address');;
        return $name;
    }

}