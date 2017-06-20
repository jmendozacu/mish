<?php

class VES_BannerManager_Model_Observer{
	
	 /**
     *
     * Hide the menu if the module is not enabled
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer){
        $resource 	= $observer->getResource();
        $result 	= $observer->getResult();

        if($resource == 'vendors/banners' && !Mage::helper('bannermanager')->moduleEnable()){
            $result->setIsAllowed(false);
        }
    }
	/**
     * Check if this feature is enabled for the current vendor (Advanced Group plugin is required)
     * @param Varien_Event_Observer $observer

    public function ves_banner_module_enable(Varien_Event_Observer $observer){
		//echo "test";exit;
        $modules = Mage::getConfig()->getNode('modules')->asArray();
        if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
            $result = $observer->getEvent()->getResult();
            if($vendor = Mage::getSingleton('vendors/session')->getVendor()){
                $groupId = $vendor->getGroupId();
				//echo $groupId;exit;
                $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('banner/enabled',$groupId);
				//var_dump($subAccountEnableConfig);exit;
                $result->setData('module_enable',$subAccountEnableConfig);
                return;
            }
        }
    }

    public function ves_banner_module_enable_app(Varien_Event_Observer $observer){
		//echo "test";exit;
        $modules = Mage::getConfig()->getNode('modules')->asArray();
        if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
            $result = $observer->getEvent()->getResult();
            if($vendor = Mage::registry("vendor")){
                $groupId = $vendor->getGroupId();
				//echo $groupId;exit;
                $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('banner/enabled',$groupId);
				//var_dump($subAccountEnableConfig);exit;
                $result->setData('is_enabled',$subAccountEnableConfig);
                return;
            }
        }
    }
	
    /**
	 * Add category block tab on app edit page.
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendorscms_cms_app_add_tab_banner(Varien_Event_Observer $observer){
		if(!Mage::helper('bannermanager')->moduleEnable()) return;
    	$tabsBlock = $observer->getEvent()->getTabs();
		$optionBlock = $tabsBlock->getLayout()->createBlock('bannermanager/app_edit_tab_banner','cms_app_edit_tab_banner');
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
			if(isset($data['banner_option'])){
				$blockAppOptionData = $data['banner_option'];
				
				$option = Mage::getModel('vendorscms/appoption');
				$data = array(
					'app_id'	=> $app->getId(),
					'code'		=> 'banner_option',
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
}