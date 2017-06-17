<?php

class Ameronix_OrderDelete_Model_Observer {

	/**
	 * @param $observer
	 */
	public function coreBlockAbstractToHtmlBefore($observer) {
		$block = $observer->getBlock();

		// Is the block the sales order grid or derived from?
		if($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction) {
			/* @var $block Mage_Adminhtml_Block_Widget_Grid_Massaction */

			if($block->getParentBlock() instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
				$block->addItem('amx_orderdelete', array(
					'label' => 'Delete Order',
					'url' => $block->getUrl('adminhtml/orderdelete/massaction'),
					'confirm' => 'Are you sure?'
				));
			}
		}
	}

	/**
	 * @param $observer
	 */
	public function salesOrderDeleteBefore($observer) {
		/* @var $order Mage_Sales_Model_Order */
		$order = $observer->getOrder();

		// Checks to see if it has an id
		if($order->getId()) {
			Mage::unregister('__amx_orderdelete_last_attempted_order');
			Mage::register('__amx_orderdelete_last_attempted_order', $order);
		}
	}

}