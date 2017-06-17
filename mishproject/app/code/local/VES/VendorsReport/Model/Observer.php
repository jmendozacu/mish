<?php
class VES_VendorsReport_Model_Observer
{
	/**
	 *
	 * Hide the menu if the module is not enabled
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer){
		$resource 	= $observer->getResource();
    	$result 	= $observer->getResult();
    	
    	if($resource == 'vendors/report' && !Mage::helper('vendorsreport')->moduleEnable()){
    		$result->setIsAllowed(false);
    	}
	}
	
	/**
	 * Check if this feature is enabled for the current vendor (Advanced Group plugin is required)
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendorsreport_module_enable(Varien_Event_Observer $observer){
		$modules = Mage::getConfig()->getNode('modules')->asArray();
		if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
			$result = $observer->getEvent()->getResult();
			if($vendor = Mage::getSingleton('vendors/session')->getVendor()){
				$groupId = $vendor->getGroupId();
				$vendorReportEnableConfig = Mage::helper('vendorsgroup')->getConfig('report/enabled',$groupId);
				$result->setData('module_enable',$vendorReportEnableConfig);
				return;
			}
		}
	}
}