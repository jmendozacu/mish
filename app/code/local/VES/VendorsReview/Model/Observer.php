<?php
/**
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_VendorsReview_Model_Observer
{
	public function vendorsreview_review_complete_after(Varien_Event_Observer $observe) {
		$review = $observe->getReview();
		/**
		 * send mail to customer
		 */
		if(Mage::helper('vendorsreview')->isCustomerSendEmail()) {
			$review->sendNewReviewEmail();
		}
	}

	/**
	 * Add review link after payment is sent.
	 * @param Varien_Event_Observer $observer
	 */
	public function sales_order_invoice_save_after(Varien_Event_Observer $observer) {
		$invoice	= $observer->getInvoice();
		$order		= $invoice->getOrder();
		if($invoice->getState() != Mage_Sales_Model_Order_Invoice::STATE_PAID) return;
		
		if(Mage::helper('vendors')->isAdvancedMode()){
			/*ADVANCED MODE*/
			if(!$order->getVendorId()) return;
			$vendor		= Mage::getModel('vendors/vendor')->load($order->getVendorId());
			/*Do nothing if the vendor is not exist*/
			if(!$vendor->getId()) return;
			
			/*Return if the link is exist.*/
			$link 		= Mage::getModel('vendorsreview/link')->getCollection()
							->addFieldToFilter('order_id',$order->getId())
							;
			if($link->count()) return;
			
			if($order->getCustomerId()) {
				$model = Mage::getModel('vendorsreview/link')->setData(array(
					'customer_id'			=>	$order->getCustomerId(),
					'vendor_id'				=> 	$vendor->getId(),
					'show_rating_link'		=>	'1',
					'can_review'			=>	'1',
					'order_id'				=>	$order->getId(),
				));
				$model->save();
				return;
			}
		}else{
			/*GENERAL MODE*/
			foreach($invoice->getAllItems() as $item){
				$orderItem 	= $item->getOrderItem();
    			if($orderItem->getParentItemId()) continue;
    			$vendorId 	= $orderItem->getVendorId();
    			$vendor		= Mage::getModel('vendors/vendor')->load($vendorId);
    			if($vendorId && $vendor->getId()){
    				/*Return if the link is exist.*/
					$link = Mage::getModel('vendorsreview/link')->getCollection()
							->addFieldToFilter('order_id',$order->getId())
							->addFieldToFilter('vendor_id',$vendorId)
							;
					if($link->count()) continue;
					
	    			if($order->getCustomerId()) {
						$model = Mage::getModel('vendorsreview/link')->setData(array(
							'customer_id'			=>	$order->getCustomerId(),
							'vendor_id'				=> 	$vendorId,
							'show_rating_link'		=>	'1',
							'can_review'			=>	'1',
							'order_id'				=>	$order->getId(),
						));
						$model->save();
					}
    			}
			}
		}
	}

	public function vendor_dashboard_grids_preparelayout(Varien_Event_Observer $observer){
		$grids = $observer->getGrids();
		$grids->addTab('last_5_review', array(
			'label'     => $grids->__('Last 5 Reviews'),
			'content'   => $grids->getLayout()->createBlock('vendorsreview/adminhtml_dashboard_review_grid')->toHtml(),
			'active'    => false
		));
	}

	public function ves_vendorspage_profile_prepare(Varien_Event_Observer $observer){
		$profileBlock = $observer->getProfileBlock();
		$ratingBlock = $profileBlock->getLayout()->createBlock('vendorsreview/vendor_rating_sidebar','vendor.rating')->setTemplate('ves_vendorsreview/vendor/rating/sidebar.phtml');
		$footerProfile = $profileBlock->getChild('footer_profile');
		$footerProfile->insert($ratingBlock, '', false, 'vendors_rating_block');
	}
	
	/*Add new column to price comparison block.*/
	public function ves_vendor_pricecomparison_prepare_columns(Varien_Event_Observer $observer){
		$block = $observer->getBlock();
		$reviewBlock = Mage::getBlockSingleton('vendorsreview/pricecomparison_vendor')->setTemplate('ves_vendorsreview/pricecomparison/vendor.phtml');
		$block->addColumn('vendor_review_rating',Mage::helper('vendorsreview')->__('Review/Rating'),$reviewBlock,100);
	}
}