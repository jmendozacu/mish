<?php
class VES_VendorsPage_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$vendorId = Mage::registry('vendor_id');
		if($vendorId){
			$vendorObj = Mage::getModel('vendors/vendor')->loadByVendorId($vendorId);
			//if(!Mage::registry('vendor_id')) Mage::register('vendor_id', $vendorId);
			if($vendorObj->getId() && ($vendorObj->getStatus() == VES_Vendors_Model_Vendor::STATUS_ACTIVATED)){
				/*You can use Mage::registry('is_vendor_homepage') to check if the current page is homepage or not*/
				Mage::register('is_vendor_homepage', true);
				
				$this->loadLayout();
				$layout = Mage::helper('vendorsconfig')->getVendorConfig('design/home/layout',$vendorObj->getId());
				$this->getLayout()->getBlock('root')->setTemplate('page/'.$layout.'.phtml');
				$this->_title($vendorObj->getTitle());
				Mage::dispatchEvent('vendor_homepage_prepare_layout',array('vendor'=>$vendorObj,'front_action'=>$this));
				
				$this->renderLayout();
				return;
			}
		}
		$this->_redirectUrl(Mage::getUrl());
	}
}