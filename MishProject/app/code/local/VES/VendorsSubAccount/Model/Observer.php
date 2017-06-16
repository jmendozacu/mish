<?php

class VES_VendorsSubAccount_Model_Observer
{
	/**
	 * Check if the vendor account is available.
	 * @param Varien_Event_Observer $observer
	 */
	public function vendor_save_before(Varien_Event_Observer $observer){
		$vendor 	= $observer->getVendor();
		$vendorId 	= explode(VES_VendorsSubAccount_Model_Account::SPECIAL_CHAR, $vendor->getVendorId());
		if(sizeof($vendorId) >1){
			$newVendorId = $vendorId[0];
			$newVendor = Mage::getModel('vendors/vendor')->loadByVendorId($newVendorId,$vendor->getWebsiteId());
			if($newVendor->getId()){
				throw Mage::exception('VES_Vendors', Mage::helper('vendorssubaccount')->__('The vendor id "%s" is not available.',$vendor->getVendorId()));
			}
		}
	}
	
	/**
	 *
	 * Hide the menu if the module is not enabled
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer){
		$resource 	= $observer->getResource();
    	$result 	= $observer->getResult();
    	
    	if($resource == 'vendors/subaccount' && !Mage::helper('vendorssubaccount')->moduleEnable()){
    		$result->setIsAllowed(false);
    	}elseif(Mage::getSingleton('vendors/session')->getIsSubAccount()){
    		$account = Mage::getSingleton('vendors/session')->getSubAccount();
    		$resources = $account->getRole()->getRoleResources();
    		if(!in_array(str_replace("vendors/","",$resource), $resources)) $result->setIsAllowed(false);
    	}
	}
	
	/**
	 * Check if this feature is enabled for the current vendor (Advanced Group plugin is required)
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendorssubaccount_module_enable(Varien_Event_Observer $observer){
		$modules = Mage::getConfig()->getNode('modules')->asArray();
		if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
			$result = $observer->getEvent()->getResult();
			if($vendor = Mage::getSingleton('vendors/session')->getVendor()){
				$groupId = $vendor->getGroupId();
				$subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('subaccount/enabled',$groupId);
				$result->setData('module_enable',$subAccountEnableConfig);
				return;
			}
		}
	}
	
	/**
	 * Check if the number of sub accounts of the current vendor must be less than the maximum number from group. 
	 * (Advanced Group plugin is required)
	 * @param Varien_Event_Observer $observer
	 */
	public function controller_action_predispatch_vendors_subaccount_account_new(Varien_Event_Observer $observer){
		$modules = Mage::getConfig()->getNode('modules')->asArray();
		if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
			$result = $observer->getEvent()->getResult();
			$session = Mage::getSingleton('vendors/session');
			if($vendor = $session->getVendor()){
				$groupId = $vendor->getGroupId();
				$subAccountLimit = Mage::helper('vendorsgroup')->getConfig('subaccount/max_subaccount',$groupId);
				
				if($subAccountLimit >0){
					$accountCollection = Mage::getModel('vendorssubaccount/account')->getCollection()->addFieldToFilter('vendor_id',$vendor->getId());
					if($subAccountLimit <= $accountCollection->count()){
						$controllerAction = $observer->getControllerAction();
						if($controllerAction->getFullActionName() == 'vendors_subaccount_account_edit' && ($id = $controllerAction->getRequest()->getParam('id'))){
							$model  = Mage::getModel('vendorssubaccount/account')->load($id);
							if($model->getId()) return;
						}
						$session->addError(Mage::helper('vendorsgroup')->__('You can only add maximum %s sub account(s)',$subAccountLimit));
						$controllerAction->setFlag('', 'no-dispatch', true);
						$controllerAction->setRedirectWithCookieCheck('vendors/subaccount_account');
					}
				}
				return;
			}
		}
	}
	
	/**
	 * Rewrite the login feature for loging in of subaccounts
	 * @param Varien_Event_Observer $observer
	 */
	public function controller_action_predispatch_vendors_index_loginPost(Varien_Event_Observer $observer){
		$controllerAction = $observer->getControllerAction();
		if ($controllerAction->getRequest()->isPost()) {
			$login = $controllerAction->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                	$account = Mage::getModel('vendorssubaccount/account');
                	if($account->authenticate($login['username'],$login['password'])){
                		$session = Mage::getSingleton('vendors/session');
                		$session->setIsSubAccount(true);
                		$session->setSubAccount($account);
                		$session->setVendor($account->getVendor());
                		$session->renewSession();
                		
	                	$this->_loginPostRedirect($controllerAction, $session);
				        
                		$controllerAction->setFlag('', 'no-dispatch', true);
                	}
                    
                } catch (Mage_Core_Exception $e) {

                } catch (Exception $e) {
                    
                }
            }
		}
	}
	
	/**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect($controllerAction,$session)
    {
    	if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('vendors')->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('vendors')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('vendors')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('vendors')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        $controllerAction->getResponse()->setRedirect($session->getBeforeAuthUrl(true));
    }
    
    /**
     * Redirect to vendor sub account profile if vendor login as subaccount
     * @param Varien_Event_Observer $observer
     */
    public function controller_action_predispatch_vendors_account_index(Varien_Event_Observer $observer){
    	$controllerAction = $observer->getControllerAction();
    	$session = Mage::getSingleton('vendors/session');
    	if($session->getIsSubAccount()){
    		$controllerAction->setFlag('', 'no-dispatch', true);
			$controllerAction->setRedirectWithCookieCheck('vendors/subaccount_profile');
    	}
    }
    
    /**
     * Check sub account permission.
     * @param Varien_Event_Observer $observer
     */
    public function controller_action_predispatch_vendors(Varien_Event_Observer $observer){
    	if(Mage::getSingleton('vendors/session')->getIsSubAccount()){
    		$account 			= Mage::getSingleton('vendors/session')->getSubAccount();
	    	$controllerAction 	= $observer->getControllerAction();
	    	$request 			= $controllerAction->getRequest();
	
	        $rounter 	= $request->getRequestedRouteName();
	        $controller = $request->getRequestedControllerName();
	        $action		= $request->getRequestedActionName();
	        $fullActionName = $controllerAction->getFullActionName('/');
	        
	        foreach($account->getNotAllowedUrls() as $url){
	        	$url = trim($url,'/');
	        	$tmpUrl = explode("/", $url);
	        	if(sizeof($tmpUrl) == 1){
	        		$url = $url.'/index/index';
	        	}elseif(sizeof($tmpUrl) == 2){
	        		$url = implode("/", $tmpUrl)."/index";
	        	}
	        	/*Redirect to profile page if the sub account do not have permission*/
	        	if($url == $fullActionName){
	        		Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorssubaccount')->__('Access denied.'));
	        		$controllerAction->setFlag('', 'no-dispatch', true);
	        		$controllerAction->setRedirectWithCookieCheck('vendors/subaccount_profile');
	        	}
	        }
    	}
    }
    /**
     * Remove message toplink if sub vendor account do not have permission.
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendorsmessage_module_enable(Varien_Event_Observer $observer){
    	/*Vendor CP*/
    	$session = Mage::getSingleton('vendors/session');
    	if(!$session->getIsSubAccount()) return;
    	
		if($account = $session->getSubAccount()){
			$resources = $account->getRole()->getRoleResources();
			if(!in_array('message',$resources)){
				$result = $observer->getEvent()->getResult();
				$result->setData('module_enable',false);
			}
		}
    }
}