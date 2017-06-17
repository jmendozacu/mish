<?php

class VES_VendorsMemberShip_Model_Observer
{
	/**
	 * Check if product is a membership package, make sure vendor is logged in.
	 * @param Varien_Event_Observer $observer
	 */
	public function catalog_product_type_prepare_full_options(Varien_Event_Observer $observer){
/*		$product = $observer->getProduct();
		if($product->getData('ves_vendor_related_group') && !Mage::getSingleton('vendors/session')->getVendorId()){
			throw new Mage_Core_Exception(Mage::helper('membership')->__('You need to login to your vendor account to buy this package.'));
		}*/
	}
	
	/**
	 * Set default expiry date when vendor register an account.
	 * @param Varien_Event_Observer $observer
	 */
	public function vendor_register_before(Varien_Event_Observer $observer){
		$vendor = $observer->getVendor();
		$period = Mage::getStoreConfig('vendors/create_account/default_group_expiry_period');

		$now 	= Mage::getModel('core/date')->timestamp();
		if($period <=1){
		  $now 	= strtotime('+'.$period.' month',$now);
		}else{
		    $now 	= strtotime('+'.$period.' months',$now);
		}
		$vendor->setExpiryDate(Mage::getModel('core/date')->date('Y-m-d H:i:s',$now));
		
		if($period <= 0){
			$vendor->setStatus(VES_Vendors_Model_Vendor::STATUS_DISABLED);
		}
	}
	
	/**
	 * Replace vendor info  block by a new one.
	 * @param Varien_Event_Observer $observer
	 */
	public function core_block_abstract_to_html_before(Varien_Event_Observer $observer){
		$block = $observer->getBlock();
		if($block instanceof VES_VendorsRelatedCustomerAccount_Block_Vendor){
			$block->unsetChild('account_info');
			$block->setChild('account_info',$block->getLayout()->createBlock('membership/customer_membership','vendor.account.membership.info',array('template'=>'ves_membership/account.phtml')));
		}
	}
	
	/**
	 * Redirect to checkout/monepage if there is a member shippackage in the shopping cart.
	 * @param Varien_Event_Observer $observer
	 */
	public function controller_action_predispatch_checkout_onepage_index(Varien_Event_Observer $observer){
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		
		if($quote->hasItems()){
			foreach($quote->getAllItems() as $item){
				if($item->getProductType() == 'membership'){
					$controllerAction = $observer->getControllerAction();
					$controllerAction->setFlag('', 'no-dispatch', true);
					$controllerAction->setRedirectWithCookieCheck('checkout/monepage');
					return;
				}
			}
			
		}
	}
	
	/**
	 * Do not allow to purchase membership item with other items.
	 * @param Varien_Event_Observer $observer
	 * @throws Mage_Core_Exception
	 */
	public function sales_quote_product_add_after(Varien_Event_Observer $observer){
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		if(!$quote->hasItems()) return;
		
		$items = $observer->getItems();
		$isMemberShipPackage = false;
		foreach($items as $item){
			if($item->getProductType() == 'membership'){
				$isMemberShipPackage = true;
				break;
			}
		}
		$hasMemberShipPackage 	= false;
		$hasNormalProduct		= false;
		
		foreach($quote->getAllItems() as $item){
			if($item->getProductType() == 'membership') {
				if($hasMemberShipPackage){
					/*There is already an other membership package in the shopping cart.*/
					throw new Mage_Core_Exception(Mage::helper('membership')->__('There is already an other membership item in the shopping cart.'));
				}else{$hasMemberShipPackage = true;}
			}
			else {$hasNormalProduct = true;}
		}
		
		if($hasMemberShipPackage && $hasNormalProduct){
			throw new Mage_Core_Exception(Mage::helper('membership')->__('Membership item can be purchased standalone only. To proceed please remove other items from the shopping cart.'));
		}
	}
	
	/**
	 * Update vendor group/expiry date if vendor buy a group package
	 * @param Varien_Event_Observer $observer
	 */
	
	public function sales_order_invoice_save_after(Varien_Event_Observer $observer){
		$invoice = $observer->getInvoice();
		if($invoice->getState() != Mage_Sales_Model_Order_Invoice::STATE_PAID) return;
		
		foreach($invoice->getAllItems() as $item){
			$orderItem 	= $item->getOrderItem();
			$product 	= Mage::getModel('catalog/product')->load($orderItem->getProductId());
			if($orderItem->getProductType() == 'membership'){
				$buyRequest = $orderItem->getProductOptionByCode('info_buyRequest');
				if($relatedGroup = $product->getData('ves_vendor_related_group')){
					if(($vendorId = $buyRequest['ves_vendor_membership'])){
						/*Upgrade for exist account*/
						if((!isset($buyRequest['ves_vendor_membership_upgraded']) || $buyRequest['ves_vendor_membership_upgraded']!=$orderItem->getId())){
							$vendor = Mage::getModel('vendors/vendor')->load($vendorId);
							if($vendor->getId()){
								$period = $product->getData('ves_vendor_period');
								
								if($vendor->getGroupId() == $relatedGroup){
									/*Add more time to expiry date*/
									$now = $vendor->getExpiryDate();
									$now = $now?strtotime($now):Mage::getModel('core/date')->timestamp();
								}else{
									/*Upgrade vendor to new group and update expiry date.*/
									$vendor->setGroupId($relatedGroup);
									$now = Mage::getModel('core/date')->timestamp();
								}
								$period = $period*$item->getQty();
								if($period <=1){
								    $now = strtotime('+'.$period.' month',$now);
								}else{
								    $now = strtotime('+'.$period.' months',$now);
								}
			    				$vendor->setExpiryDate(Mage::getModel('core/date')->date('Y-m-d H:i:s',$now));
			    				$vendor->save();
			    				
			    				/*Make sure when the invoice is save this item is not processed again*/
			    				$options = $orderItem->getProductOptions();
			    				$buyRequest['ves_vendor_membership_upgraded'] = $orderItem->getId();
			    				$options['info_buyRequest'] = $buyRequest;
			    				$orderItem->setProductOptions($options)->save();
							}
						}
					}elseif(isset($buyRequest['vendor_info'])){
						//$session = Mage::getSingleton('adminhtml/session');
						$errors = array();
						try{
							$vendorInfo = $buyRequest['vendor_info'];
							$vendor = Mage::getModel('vendors/vendor')->setId(null);
							/**
				             * Initialize customer group id
				             */
				            $vendor->getGroupId();
				            $vendor->setData($vendorInfo);
				        	$vendor->setPassword($vendorInfo['password']);
				        	$vendor->setConfirmation($vendorInfo['confirm_password']);
				        	
				        	$storeId = $orderItem->getOrder()->getStoreId();
				        	$websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
				        	$vendor->setStoreId($storeId);
				        	$vendor->setWebsiteId($websiteId);
				        	
							$vendorErrors = $vendor->validate();
				        	if (is_array($vendorErrors)) {
				        		$errors = array_merge($vendorErrors, $errors);
				        	}
				        	$validationResult = count($errors) == 0;
				        	if (true === $validationResult) {
				        		Mage::dispatchEvent('vendor_register_before',
			                        array('account_controller' => $this, 'vendor' => $vendor)
			                    );
			                    
					        	$vendor->setStatus(VES_Vendors_Model_Vendor::STATUS_ACTIVATED);
				        		/*Set group for vendor to purchased group package.*/
			                	$vendor->setGroupId($relatedGroup);
			                	/*Update expiry date*/
			                	$period = $product->getData('ves_vendor_period');
			                	$now = Mage::getModel('core/date')->timestamp();
			                	$period = $period*$item->getQty();
			                	if($period <=1){
			                	    $now = strtotime('+'.$period.' month',$now);
			                	}else{
			                	    $now = strtotime('+'.$period.' months',$now);
			                	}
			                	$vendor->setExpiryDate(Mage::getModel('core/date')->date('Y-m-d H:i:s',$now));

			                    $vendor->save();
			
			                    Mage::dispatchEvent('vendor_register_success',
			                        array('account_controller' => $this, 'vendor' => $vendor)
			                    );
			
			                    /*if ($vendor->isConfirmationRequired()) {
			                        $vendor->sendNewAccountEmail(
			                            'confirmation',
			                            '',
			                            Mage::app()->getStore()->getId()
			                        );
			                        return;
			                    } else {*/
			                    	if(Mage::helper('vendors')->approvalRequired()){
								        $vendor->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
			                    	}else{
			                    		if(!Mage::app()->getStore()->isAdmin()){
			                    			Mage::getSingleton('vendors/session')->setVendorAsLoggedIn($vendor);
					                        $vendor->sendNewAccountEmail(
									            'registered',
									            '',
									            Mage::app()->getStore()->getId()
									        );
			                    		}
			                    	}
			                        return;
			                    /*}*/
				        	}
				        	
						}catch (Exception $e) {
							if(Mage::app()->getStore()->isAdmin()){
								$session = Mage::getSingleton('adminhtml/session');
							}else{
								$session = Mage::getSingleton('core/session');
							}
			                $session->addException($e, $e->getMessage());
			            }
					}
				}
			}
			
		}
	}

	/**
	 * Daily check expired vendor (cron jobs)
	 */
	public function dailyCheckExpiredVendor(){
		try{
	    	$resource 			= Mage::getSingleton('core/resource');
	    	$writeConnection 	= $resource->getConnection('core_write');
	    	$table 				= $resource->getTableName('vendors/vendor');
	    	
	        $now 		= Mage::getModel('core/date')->timestamp();
	        $action 	= Mage::getStoreConfig('vendors/create_account/expiry_action');
	        
	        if($action == 'disable_account'){
	        	$query = "UPDATE `$table` SET status=".VES_Vendors_Model_Vendor::STATUS_DISABLED.",updated_at='".Mage::getModel('core/date')->date('Y-m-d H:i:s',$now)."' WHERE expiry_date<'".Mage::getModel('core/date')->date('Y-m-d H:i:s',$now)."'";
	        }elseif(strpos($action, 'change_group')!== false){
	        	$action = explode("||", $action);
	        	$groupId = $action[1];
	        	$newExpiry	= strtotime('+12 months',$now);
	        	$newExpiry	= Mage::getModel('core/date')->date('Y-m-d H:i:s',$newExpiry);
	        	$query = "UPDATE `$table` SET group_id='".$groupId."',expiry_date='".$newExpiry."',updated_at='".Mage::getModel('core/date')->date('Y-m-d H:i:s',$now)."' WHERE expiry_date<'".Mage::getModel('core/date')->date('Y-m-d H:i:s',$now)."'";
	        }
			$writeConnection->query($query);
			
	        Mage::log('Check expired vendor for date',Zend_Log::INFO,'ves_vendor_membership.log');
    	}catch(Mage_Core_Exception $e){
    		Mage::log($e->getMessage(),Zend_Log::ERR,'ves_vendor_membership.log');
    	}catch (Exception $e){
    		Mage::log($e->getMessage(),Zend_Log::ERR,'ves_vendor_membership.log');
    	}
	}
	
	/**
	 * Add expiry date to vendor account info
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendors_account_edit_tab_main_after(Varien_Event_Observer $observer){
		$tab 	= $observer->getTab();
		$form 	= $observer->getForm();
		$fieldSet = $form->getElement('vendors_form');
		$fieldSet->addField('expiry_date', Mage::app()->getStore()->isAdmin()?'date':'label', array(
		  'label'     => Mage::helper('vendors')->__('Expired Date'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'image'	  => $tab->getSkinUrl('images/grid-cal.gif'),
		  'format'	  => Varien_Date::DATE_INTERNAL_FORMAT,
		  'name'      => 'expiry_date',
		),'group_id');
		
		if ( Mage::registry('vendors_data') ) {
			$expiryDate = Mage::helper('core')->formatDate(Mage::registry('vendors_data')->getData('expiry_date'));
		  	$form->getElement('expiry_date')->setValue($expiryDate);
		}
	}
	
	/**
	 * Add priority field to group
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendors_group_prepare_tabs_after(Varien_Event_Observer $observer){
	    $tab = $observer->getTabs();
	    $newMainTabBlock = $tab->getLayout()->createBlock('membership/adminhtml_vendors_group');
	    $tab->setTabData('main_section', 'content', $newMainTabBlock->toHtml());
	}
}