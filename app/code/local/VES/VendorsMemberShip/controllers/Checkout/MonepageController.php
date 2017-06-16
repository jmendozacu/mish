<?php
include 'Mage/Checkout/controllers/OnepageController.php';
class VES_VendorsMemberShip_Checkout_MonepageController extends Mage_Checkout_OnepageController
{
	protected function _getPackageItem(){
		return $this->getOnepage()->getQuote()->getItemsCollection()->getFirstItem();
	}
	
    /**
     * Predispatch: should set layout area
     *
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $quote = $this->getOnepage()->getQuote();
        if(!$quote->hasItems()|| $quote->getHasError()) return;
        
        $check 	= true;
        $quote 	= $this->getOnepage()->getQuote(); 
        $numberOfItems = 0;
        foreach($quote->getAllVisibleItems() as $item){
        	$numberOfItems++;
        }
        if($numberOfItems > 1) $check = false;
        
        $item 	= $this->_getPackageItem();
        if($item->getProduct()->getTypeId() != 'membership') $check = false;
        
        if (!$check) {
            $this->_redirect('checkout/onepage');
            $this->setFlag('',self::FLAG_NO_DISPATCH,true);
            return;
        }

        $vendorSession = Mage::getSingleton('vendors/session');
        if($vendorSession->isLoggedIn()){
            $packageRelatedGroup = Mage::getResourceModel('catalog/product')->getAttributeRawValue($item->getProductId(),'ves_vendor_related_group');
            $relatedGroup = Mage::getModel('vendors/group')->load($packageRelatedGroup);
            $packagePriority = $relatedGroup->getPriority();

            $group = $vendorSession->getVendor()->getGroup();
            $currentGroupPriority = $group->getPriority();
            if($currentGroupPriority < $packagePriority){
                Mage::getSingleton('checkout/session')->addError($this->__("You are in %s membership you can not buy this package.",'<strong>'.$group->getName().'</strong>'));
                $this->_redirect('checkout/cart');
                $this->setFlag('',self::FLAG_NO_DISPATCH,true);
                return;
            }

        }
        
    }
    
    
	public function saveBillingAction(){
		if ($this->_expireAjax()) {
            return;
        }
        
        if ($this->getRequest()->isPost()) {
        	$data = $this->getRequest()->getPost('billing', array());
        	$vendorData = $this->getRequest()->getPost('vendor',array());
        	if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            
            /*Validate vendor information*/
        	if(!Mage::getSingleton('vendors/session')->isLoggedIn()){
        		$errors = array();
		        if (!isset($data['firstname']) || !Zend_Validate::is( trim($data['firstname']) , 'NotEmpty')) {
		            $errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
		        }
		        
		        if (!isset($data['lastname']) || !Zend_Validate::is( trim($data['lastname']) , 'NotEmpty')) {
		            $errors[] = Mage::helper('customer')->__('The last name cannot be empty.');
		        }
		
		        if (!isset($data['email']) || !Zend_Validate::is($data['email'], 'EmailAddress')) {
		            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $data['email']);
		        }
				
        		if (!isset($vendorData['vendor_id']) || !Zend_Validate::is( trim($vendorData['vendor_id']) , 'NotEmpty')) {
		            $errors[] = Mage::helper('membership')->__('Vendor id can not be empty.');
		        }
		        
		        $password = $vendorData['password'];
		        if (!Zend_Validate::is($password , 'NotEmpty')) {
		            $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
		        }
		        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
		            $errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
		        }
		        $confirmation = $vendorData['confirm_password'];
		        if ($password != $confirmation) {
		            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
		        }
	        	
	        	/*Check if the vendor id is exist*/
        		$vendor = Mage::getModel('vendors/vendor')->loadByVendorId($vendorData['vendor_id']);
        		if($vendor->getId()){
        			$errors[] = Mage::helper('membership')->__('The vendor id \'%s\' is already exist.',$vendorData['vendor_id']);
        		}
        		
        		/*Check if the vendor id is exist*/
        		$vendor = Mage::getModel('vendors/vendor')->loadByEmail($data['email']);
        		if($vendor->getId()){
        			$errors[] = Mage::helper('membership')->__('The email \'%s\' is already in use.',$data['email']);
        		}
        		if(sizeof($errors)){
        			$result = array('error'=>true, 'message'=>implode("\n",$errors));
        			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	        		return;
        		}
        		
        		$item 		= $this->_getPackageItem();
        		$buyRequest = $item->getBuyRequest();
        		$vendorData = array_merge($data,$vendorData);
        		if(isset($vendorData['street']) && is_array($vendorData['street']))
        			$vendorData['address'] = implode(' ',$vendorData['street']);
        		unset($vendorData['customer_password']);
        		unset($vendorData['address_id']);
        		unset($vendorData['street']);
        		unset($vendorData['use_for_shipping']);
        		
        		$buyRequest->setData('vendor_info',$vendorData);
        		$item->getOptionByCode('info_buyRequest')->setValue(serialize($buyRequest->getData()));
        	}else{
        		$item 		= $this->_getPackageItem();
        		$buyRequest = $item->getBuyRequest();
        		$buyRequest->setData('ves_vendor_membership',Mage::getSingleton('vendors/session')->getVendorId());
        		$item->getOptionByCode('info_buyRequest')->setValue(serialize($buyRequest->getData()));
        	}
        	
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
	}
}