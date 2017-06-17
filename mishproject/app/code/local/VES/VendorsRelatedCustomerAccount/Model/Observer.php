<?php

class VES_VendorsRelatedCustomerAccount_Model_Observer
{
	/**
	 * If the customer account is logged it and it associated to a vendor account just reject vendor login 
	 * @param unknown_type $observer
	 */
	public function vendors_controller_pre_dispatch_before(Varien_Event_Observer $observer){
		if(!Mage::getStoreConfig('vendors/create_account/vendor_auto_login')) return;
		
    	$vendorSession 		= Mage::getSingleton('vendors/session');    	
    	Mage::getSingleton('core/session',array('name'=>'frontend'));
    	$customerSession 	= Mage::getSingleton('customer/session');

    	
    	if($customerSession->isLoggedIn()){
    		$vendor = Mage::getModel('vendors/vendor')->loadByAttribute('customer_id',$customerSession->getCustomerId());
    		if($vendor->getId()){
    			if ($vendor->getConfirmation() && $vendor->isConfirmationRequired()) {
    				$value = Mage::helper('vendors')->getEmailConfirmationUrl($vendor->getVendorId());
		            Mage::getSingleton('vendors/session')->addError(Mage::helper('vendors')->__('Your vendor account "%s" is not confirmed. <a href="%s">Click here</a> to resend confirmation email.',$vendor->getVendorId(), $value));
		        }elseif ($vendor->getStatus() == VES_Vendors_Model_Vendor::STATUS_PENDING){
		        	Mage::getSingleton('vendors/session')->addError(Mage::helper('vendors')->__('Your vendor account "%s" is awaiting for approval.',$vendor->getVendorId()));
		        }elseif ($vendor->getStatus() == VES_Vendors_Model_Vendor::STATUS_DISABLED) {
		            Mage::getSingleton('vendors/session')->addError(Mage::helper('vendors')->__('Your vendor account "%s" has been suppended.',$vendor->getVendorId()));
		        }else{
		        	if(!$vendorSession->isLoggedIn() || ($vendorSession->getVendor()->getCustomerId()!=$customerSession->getCustomerId())){
		        		$vendorSession->setVendor($vendor);
		        	}
		        }
    		}
    	}
    }
    
    /**
     * Auto login to customer account
     * @param unknown_type $observer
     */
    public function controller_action_predispatch_customer(Varien_Event_Observer $observer){
    	$vendorSession 		= Mage::getSingleton('vendors/session');
    	$customerSession 	= Mage::getSingleton('customer/session');
    	$vendor 			= $vendorSession->getVendor();
    	if($vendorSession->isLoggedIn() && !$customerSession->isLoggedIn() && $vendor->getCustomerId()){
    		$customer = Mage::getModel('customer/customer')->load($vendor->getCustomerId());
    		$customerSession->setCustomer($customer);
    	}
    }
    
    /**
     * Auto logout vendor account
     * @param unknown_type $observer
     */
    public function controller_action_predispatch_customer_account_logout(Varien_Event_Observer $observer){
    	$action = $observer->getControllerAction();
    	Mage::getSingleton('vendors/session')->logout();
    }
    
    /**
     * Set flag to signup vendor
     * @param unknown_type $observer
     */
    public function controller_action_predispatch_customer_account_createpost(Varien_Event_Observer $observer){
    	$action = $observer->getControllerAction();
    	$createVendor = $action->getRequest()->getPost('vendor_is_signed_up',false);
    	if($createVendor){
    		Mage::register('sign_up_vendor', 1);
    	}
    }

	/**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeVendor(VES_Vendors_Model_Vendor $vendor, $isJustConfirmed = false)
    {
        Mage::getSingleton('customer/session')->addSuccess(
            Mage::helper('vendors')->__('Your vendor account has been created.', Mage::app()->getStore()->getFrontendName())
        );

        $vendor->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
        if (Mage::getSingleton('customer/session')->getBeforeAuthUrl()) {
            $successUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
    
    /**
     * Save vendor
     * @param Varien_Event_Observer $observer
     */
    public function customer_save_after(Varien_Event_Observer $observer){
    	/*Register new customer and vendor account*/
    	if(Mage::registry('sign_up_vendor') && !Mage::registry('registered_vendor')){
    		$customer = $observer->getCustomer();
    		$vendor = Mage::getModel('vendors/vendor')->setId(null);
    		$session = Mage::getSingleton('customer/session');
    		Mage::register('registered_vendor',true);
    		try {
            	$data = Mage::app()->getRequest()->getPost();
	            if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
					try {
						/* Starting upload */	
						$uploader = new Varien_File_Uploader('logo');
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(true);
						$uploader->setFilesDispersion(true);
						$path = Mage::getBaseDir('media') . DS."ves_vendors".DS."logo".DS ;
						$uploader->save($path, $_FILES['logo']['name']);
						$data['logo'] = "ves_vendors/logo".$uploader->getUploadedFileName();
					} catch (Exception $e) {
			      		
			        }
				}
				
            	$vendor->setData($data);
            	
	        	$vendor->setPassword(Mage::app()->getRequest()->getPost('password'));
	        	$vendor->setConfirmation(Mage::app()->getRequest()->getPost('confirmation'));
	        	$customerErrors = $vendor->validate();
	        	if (is_array($customerErrors)) {
	        		$errors = array_merge($customerErrors, $errors);
	        	}
                
                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                	if(!Mage::helper('vendors')->approvalRequired() && !$vendor->isConfirmationRequired()){
                		$vendor->setStatus(VES_VEndors_Model_Vendor::STATUS_ACTIVATED);
                	}
                	$vendor->setGroupId(Mage::getStoreConfig('vendors/create_account/default_group'));
                	$vendor->setCustomerId($customer->getId());
                	Mage::dispatchEvent('vendor_register_before',
                        array('account_controller' => Mage::app()->getFrontController(), 'vendor' => $vendor)
                    );
                    
                    $vendor->save();

                    Mage::dispatchEvent('vendor_register_success',
                        array('account_controller' => Mage::app()->getFrontController(), 'vendor' => $vendor)
                    );

                    if ($vendor->isConfirmationRequired()) {
                        $vendor->sendNewAccountEmail(
                            'confirmation',
                            $session->getBeforeAuthUrl(),
                            Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess(Mage::helper('vendors')->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('vendors')->getEmailConfirmationUrl($vendor->getEmail())));
                        return;
                    } else {
                    	if(Mage::helper('vendors')->approvalRequired()){
                    		$session->addSuccess(
					            Mage::helper('vendors')->__('Thank you for registering with %s. Your vendor account info is submited for approval.', Mage::app()->getStore()->getFrontendName())
					        );
                    	}else{
	                        $session->setVendorAsLoggedIn($vendor);
	                        $url = $this->_welcomeVendor($vendor);
                    	}
                        return;
                    }
                } else {
                    $session->setVendorFormData(Mage::app()->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError(Mage::helper('vendors')->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setVendorFormData(Mage::app()->getRequest()->getPost());
                if ($e->getCode() === VES_Vendors_Model_Vendor::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('vendors/account/forgotpassword');
                    $message = Mage::helper('vendors')->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                }elseif($e->getCode() === VES_Vendors_Model_Vendor::EXCEPTION_VENDOR_ID_EXISTS){
                	$url = Mage::getUrl('vendors/account/forgotpassword');
                    $message = Mage::helper('vendors')->__('There is already an account with this vendor id. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setVendorFormData(Mage::app()->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the vendor.'));
            }
    	}else{
    		$customer = $observer->getCustomer();
    		if($customer->getPassword()){
    			$vendor = Mage::getModel('vendors/vendor')->loadByAttribute('customer_id',$customer->getId());
	    		if($vendor->getId()){
	    			$vendor->setPassword($customer->getPassword())->save();
	    		}
    		}
    	}
    }
}