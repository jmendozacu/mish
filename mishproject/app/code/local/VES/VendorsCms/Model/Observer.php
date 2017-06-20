<?php

class VES_VendorsCms_Model_Observer
{	
	/**
	 * Add static block tab on app edit page.
	 * @param unknown_type $observer
	 */
	public function ves_vendorscms_cms_app_add_tab_static_block(Varien_Event_Observer $observer){
		$tabsBlock = $observer->getEvent()->getTabs();
		$optionBlock = $tabsBlock->getLayout()->createBlock('vendorscms/vendor_app_edit_tab_block','cms_app_edit_tab_block');
		$tabsBlock->addTab('frontend_app_options', $optionBlock);
	}
	
	/**
	 * Save Block app option 
	 * @param Varien_Event_Observer $observer
	 */
	public function vendorscms_app_save_after(Varien_Event_Observer $observer){
		$app 		= $observer->getEvent()->getApp();
		$request 	= $observer->getEvent()->getRequest();
		if($data = $request->getPost()){
			/*Save frontend instances*/
			if(isset($data['block_app_option'])){
				$blockAppOptionData = $data['block_app_option'];
				
				$option = Mage::getModel('vendorscms/appoption');
				$data = array(
					'app_id'	=> $app->getId(),
					'code'		=> 'block_app_option',
				);
				$option->setData($data);
				if($blockAppOptionData['option_id']){
					$option->setId($blockAppOptionData['option_id']);
				}
				unset($blockAppOptionData['option_id']);
								
				$option->setData('value',json_encode($blockAppOptionData));
				$option->save();
			}
		}
	}
	
	/**
	 * Process frontend instance app
	 * @param Varien_Event_Observer $observer
	 */
	public function controller_action_layout_generate_blocks_after(Varien_Event_Observer $observer){
		if(!Mage::helper('vendorscms')->moduleEnable()) return;
		$action = $observer->getEvent()->getAction();
		$layout = $observer->getEvent()->getLayout();
		if($vendor = Mage::registry('vendor')){
			/*Remove all default blocks*/
			$leftBlock = $layout->getBlock('left');
			//$layout->getBlock('left')->unsetChildren();
			if($leftBlock) foreach($leftBlock->getChild() as $key=>$value){
				if(substr($key, 0,7) == 'vendors') continue;
				$leftBlock->unsetChild($key);
			}
			
			$rightBlock = $layout->getBlock('right');
			//$layout->getBlock('left')->unsetChildren();
			if($rightBlock) foreach($rightBlock->getChild() as $key=>$value){
				if(substr($key, 0,7) == 'vendors') continue;
				$rightBlock->unsetChild($key);
			}
			
			$layout->getBlock('footer')->unsetChildren();
			
			/*Remove default top menu*/
			$layout->getBlock('top.menu')->unsetChildren();
			
			/*Remove default top search*/
			//$layout->getBlock('top.search')->addAttribute('ignore', true);
			/*Change Logo to vendor logo*/
			$headerBlock = $layout->getBlock('header');
			$headerBlock->setLogo($vendor->getLogo(),$vendor->getTitle());
			/*Process frontend instance*/
			$apps = Mage::getModel('vendorscms/app')->getCollection()->addFieldToFilter('vendor_id',$vendor->getId())->addOrder('sort_order','ASC');
			foreach($apps as $app){
				Mage::helper('vendorscms')->processFrontendApp($app,$layout,$action);
			}
			
			if($vendorConfig = Mage::helper('vendorsconfig')->getVendorConfig('design/config/theme',$vendor->getId())){
				$vendorConfig = explode("/", $vendorConfig);
				Mage::getDesign()->setPackageName($vendorConfig[0]);
				Mage::getDesign()->setTheme($vendorConfig[1]);
			}
		}
	}
	
	/**
	 * Check menu permission
	 * @param unknown_type $observer
	 */
	public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer){
    	$resource 	= $observer->getResource();
    	$result 	= $observer->getResult();
    	if($resource == 'vendors/cms' && !Mage::helper('vendorscms')->isEnabledModule()){
    		$result->setIsAllowed(false);
    	}
    }
    /**
     * Check controller permission
     * @param Varien_Event_Observer $observer
     */
    public function vendors_controller_pre_dispatch(Varien_Event_Observer $observer){
    	$action = $observer->getAction();
    	/*
    	if((strpos($action->getRequest()->getControllerName(),'cms') !== false) && !Mage::helper('vendorscms')->isEnabledModule()){
    		$action->setFlag('', $action::FLAG_NO_DISPATCH, true);
            $action->setRedirectWithCookieCheck('no-route');
            return;
    	}
    	*/
    }
    
    /**
     * Remove all fields from vendor page config and some fields from vendor whom does not access
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendorsconfig_form_fieldset_prepare_before(Varien_Event_Observer $observer){
    	$fieldsetId = $observer->getEvent()->getGroupId();
    	if(!Mage::helper('vendorscms')->moduleEnable()){
    		if($fieldsetId == 'design_config'){
    			$group	= $observer->getEvent()->getGroup();
    			$fields = $group->getFields();
    			unset($fields['cms_home_page']);
    			unset($fields['show_cms_breadcrumbs']);
    			$group->setData('fields',$fields);
    		}
    		return;
    	}
    	
    	if($fieldsetId == 'design_home'){
    		$group	= $observer->getEvent()->getGroup();
    		$group->setData('fields',null);
    	}elseif($fieldsetId == 'design_config'){
    		$group	= $observer->getEvent()->getGroup();
    		//$fields = $group->getFields();
    		//unset($fields['cms_home_page']);
    		//unset($fields['show_cms_breadcrumbs']);
    		//$group->setData('fields',$fields);
    	}
    }
}