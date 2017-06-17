<?php
class VES_VendorsRelatedCustomerAccount_VendorController extends Mage_Core_Controller_Front_Action
{
	protected function _getSession(){
		return Mage::getSingleton('customer/session');
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
            $this->__('Thank you for registering with %s. Your vendor account has been created', Mage::app()->getStore()->getFrontendName())
        );

        $vendor->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
    
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

    	if (!$this->_getSession()->authenticate($this)) {
        	$this->setFlag('', 'no-dispatch', true);
        }
    }
    
	public function indexAction(){
		$this->loadLayout()->initLayoutMessages('customer/session');
		$this->getLayout()->getBlock('head')->setTitle('Vendor Dashboard');
		$this->renderLayout();
	}
	
	public function registerPostAction(){
		$customer = $this->_getSession()->getCustomer();
		$password = $this->getRequest()->getPost('password');
		/*customer_save_after action is catched by observer so we just need to save customer without change any customer info*/
		
		$vendor = Mage::getModel('vendors/vendor')->setId(null);
		$session = Mage::getSingleton('customer/session');
		try {
			$customer->authenticate($customer->getEmail(),$password);
			
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
			
			$data['firstname'] = $customer->getData('firstname');
			$data['lastname'] = $customer->getData('lastname');
			$data['email'] = $customer->getData('email');
			
			$vendor->setData($data);
			
			$vendor->setPassword(Mage::app()->getRequest()->getPost('password'));
			$vendor->setConfirmation(Mage::app()->getRequest()->getPost('password'));
			$customerErrors = $vendor->validate();
			if (is_array($customerErrors)) {
				$errors = array_merge($customerErrors, $errors);
			}
			
			$validationResult = count($errors) == 0;

			if (true === $validationResult) {
				if(!Mage::helper('vendors')->approvalRequired() && !$vendor->isConfirmationRequired()){
					$vendor->setStatus(VES_Vendors_Model_Vendor::STATUS_ACTIVATED);
				}
				$vendor->setGroupId(Mage::getStoreConfig('vendors/create_account/default_group'));
				$vendor->setCustomerId($customer->getId());
				
				Mage::dispatchEvent('vendor_register_before',
                	array('account_controller' => $this, 'vendor' => $vendor)
                );
                
				$vendor->save();

				Mage::dispatchEvent('vendor_register_success',
					array('account_controller' => $this, 'vendor' => $vendor)
				);

				if ($vendor->isConfirmationRequired()) {
					$vendor->sendNewAccountEmail(
						'confirmation',
						$session->getBeforeAuthUrl(),
						Mage::app()->getStore()->getId()
					);
					$session->addSuccess(Mage::helper('vendors')->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('vendors')->getEmailConfirmationUrl($vendor->getEmail())));
					$this->_redirect('*/*');
				} else {
					if(Mage::helper('vendors')->approvalRequired()){
						$session->addSuccess(
							Mage::helper('vendors')->__('Thank you for registering with %s. Your vendor account info is submited for approval.', Mage::app()->getStore()->getFrontendName())
						);
					}else{
						$session->setVendorAsLoggedIn($vendor);
						$url = $this->_welcomeVendor($vendor);
					}
					$this->_redirect('*/*');
				}
			} else {
				$session->setFormData(Mage::app()->getRequest()->getPost());
				if (is_array($errors)) {
					foreach ($errors as $errorMessage) {
						$session->addError($errorMessage);
					}
				} else {
					$session->addError(Mage::helper('vendors')->__('Invalid customer data'));
				}
			}
		} catch (Mage_Core_Exception $e) {
			$session->setFormData(Mage::app()->getRequest()->getPost());
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
			$session->setFormData(Mage::app()->getRequest()->getPost())
				->addException($e, $this->__('Cannot save the vendor.'));
		}
		$this->_redirect('*/*');
	}
}