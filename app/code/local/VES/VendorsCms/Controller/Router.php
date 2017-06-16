<?php
class VES_VendorsCms_Controller_Router{
	/**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function vendor_cms_controller_router_match($observer)
    {
    	if(!Mage::helper('vendorscms')->isEnabledModule()) return;
    	
        $front 		= $observer->getEvent()->getRouter();
        $condition 	= $observer->getEvent()->getCondition();
        $request	= $observer->getEvent()->getRequest();
        $identifier = $condition->getIdentifier();
        $vendorId 	= Mage::registry('vendor')->getId();
        
        $identifier = $identifier?$identifier:Mage::helper('vendorscms')->getDefaultCmsHomePage();
        $page   = Mage::getModel('vendorscms/page');
        $pageId = $page->checkIdentifier($identifier,$vendorId);
        
        if (!$pageId) {
            return false;
        }
        $condition->setDispatched(true);
        $request->setModuleName('vendorscms')
            ->setControllerName('page')
            ->setActionName('view')
            ->setParam('page_id', $pageId);
        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );
        if(!$condition->getIdentifier() && !Mage::registry('is_vendor_homepage')){
        	Mage::register('is_vendor_homepage', true);
        }
    }
	
}