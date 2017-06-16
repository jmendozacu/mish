<?php
class VES_VendorsCms_PageController extends Mage_Core_Controller_Front_Action
{
	public function viewAction(){
		$page = Mage::getModel('vendorscms/page')->load($this->getRequest()->getParam('page_id'));
		Mage::register('vendorscms_page',$page);
		$this->loadLayout();
		$this->renderLayout();
	}
}