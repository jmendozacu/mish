<?php

	class Nanowebgroup_HybridMobile_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
	{
	    public function getMessage()
	    {
	    	/*
	    	 * Here you have check if there's a message to be displayed or not
	    	*/
	    	$url = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/hybrid_mobile");
	    	$validate = Mage::helper('hybridmobile')->validateAction();
	    	$email = Mage::getStoreConfig('hybrid_mobile/registration/email', Mage::app()->getStore());
	    	$message = "";
	    	if($email == '' || $email == NULL) {
	    		$message = 'To enable your mobile store, please complete your registration by adding a billing e-mail. <a href="'.$url.'">Launch Mobile Store</a>';
	    	} else if(!$validate) {
	        	$message = 'Your free trial has expired, please <a href="http://nanowebgroup.com/product/giganto-mobile-ecommerce-for-magento-2/" target="_blank">Subscribe to Giganto™</a>' ;
	        }
	        return $message;
	    }

	    public function displayMessage() {
	    	$email = Mage::getStoreConfig('hybrid_mobile/registration/email', Mage::app()->getStore());
	    	$validate = Mage::helper('hybridmobile')->validateAction();

	    	if($email == '' || $email == NULL) {
	            return true;
	        } else if (!$validate) {
	        	return true;
	        } else {
	        	return false;
	        }
	    }
	}
?>