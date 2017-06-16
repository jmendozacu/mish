<?php
class VES_VendorsReview_Vendor_ReviewController extends VES_Vendors_Controller_Action
{
	public function indexAction(){
    	$this->loadLayout()
		->_setActiveMenu('vendorsreview/review')->_title(Mage::helper('vendorsreview')->__('Reviews and Ratings'))->_title(Mage::helper('vendorsreview')->__('Manage Reviews'))
    	->_addBreadcrumb(Mage::helper('vendorsreview')->__('Reviews and Ratings'), Mage::helper('vendorsreview')->__('Reviews and Ratings'))
    	->_addBreadcrumb(Mage::helper('vendorsreview')->__('Manage Reviews'), Mage::helper('vendorsreview')->__('Manage Reviews'));
		$this->renderLayout();
	}
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendorsreview/review')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('vendors/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('review_data', $model);

			$this->loadLayout()->_setActiveMenu('vendorsreview/review/edit')->_title(Mage::helper('vendorsreview')->__('Reviews and Ratings'))->_title(Mage::helper('vendorsreview')->__('Manage Reviews'))
    			->_addBreadcrumb(Mage::helper('vendorsreview')->__('Reviews and Ratings'), Mage::helper('vendorsreview')->__('Reviews and Ratings'))
    			->_addBreadcrumb(Mage::helper('vendorsreview')->__('Manage Reviews'), Mage::helper('vendorsreview')->__('Manage Reviews'));
			if ($model->getId()){
				$this->_title(Mage::helper('vendorsreview')->__('View Review'))
				->_addBreadcrumb(Mage::helper('vendorsreview')->__('View Review'), Mage::helper('vendorsreview')->__('View Review'));
			}else{
				$this->_title(Mage::helper('vendorsreview')->__('Add Review'))
				->_addBreadcrumb(Mage::helper('vendorsreview')->__('Add Review'), Mage::helper('vendorsreview')->__('Add Review'));
			}
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->renderLayout();
		} else {
			Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorsreview')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function testAction() {
		$orders = Mage::getModel('sales/order')
		->getCollection()
		->addAttributeToSelect('*')
		->addFieldToFilter('status','complete')
		->addFieldToFilter('customer_id', 3)
		->setOrder('created_at');
		
		foreach($orders as $_order) {
			var_dump($_order->getData());
		}
	}

}