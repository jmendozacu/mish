<?php
class VES_VendorsReview_RatingController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		if(!$this->getRequest()->getParam('order_id')) {
			Mage::register('no_rating', true);
		} else {
				$order_id = $this->getRequest()->getParam('order_id');
				$link = Mage::getModel('vendorsreview/link')->load($order_id,'order_id');
				if($link->getCanReview() == null) {
					Mage::register('no_rating', true);
					Mage::getSingleton('core/session')->addError('You do not have permission to access this page.');
					$this->_redirectUrl(Mage::helper('vendorspage')->getUrl('rating/index',Mage::registry('vendor_id')));
					return;
				} else if($link->getCanReview() == '0') {
					Mage::register('no_rating', true);
					Mage::getSingleton('core/session')->addError('You have leaved a review for this order.');
					$this->_redirectUrl(Mage::helper('vendorspage')->getUrl('rating/index',Mage::registry('vendor_id')));
					return;
				}
		}
		$this->loadLayout()->renderLayout();
	}
}