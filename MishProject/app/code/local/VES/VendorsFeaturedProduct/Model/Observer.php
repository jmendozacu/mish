<?php

class VES_VendorsFeaturedProduct_Model_Observer extends Varien_Object
{
	/**
	 * Add category block tab on app edit page.
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendorscms_cms_app_add_tab_feature(Varien_Event_Observer $observer){
		$tabsBlock = $observer->getEvent()->getTabs();
		$optionBlock = $tabsBlock->getLayout()->createBlock('vendorsfeaturedproduct/app_edit_tab_feature','cms_app_edit_tab_featureproduct');
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
			if(isset($data['featureproduct_option'])){
				$blockAppOptionData = $data['featureproduct_option'];
	
				$option = Mage::getModel('vendorscms/appoption');
				$data = array(
						'app_id'	=> $app->getId(),
						'code'		=> 'featureproduct_option',
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