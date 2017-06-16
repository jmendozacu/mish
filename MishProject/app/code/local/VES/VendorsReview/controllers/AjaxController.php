<?php
class VES_VendorsReview_AjaxController extends Mage_Core_Controller_Front_Action {
	/**
	 * hidden link when customer press hidden
	 */
	public function hiddenAction() {
		$order_id = $this->getRequest()->getParam('order');
		$link = Mage::getModel('vendorsreview/link')->load($order_id,'order_id');
		
		$link->setData('show_rating_link','0');
		$link->save();
	}
}