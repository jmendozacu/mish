<?php

class Ameronix_OrderDelete_Adminhtml_OrderdeleteController
	extends Mage_Adminhtml_Controller_Action {

	/**
	 * @param $orderIds
	 */
	protected function delete($orderIds) {
		if(!$orderIds) {
			$this->_getSession()->addError('No Order(s) Specified!');
			return;
		}

		if(!is_array($orderIds)) {
			$orderIds = array($orderIds);
		}

		$orders = Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('entity_id', array('in' => $orderIds))
			->setOrder('entity_id', 'desc');

		if($orders->count() > 0) {
			$newestId = $orders->getFirstItem()->getId();
			$oldestId = $orders->getLastItem()->getId();

			try {
				// Generates a tranaction model
				$transaction = Mage::getModel('core/resource_transaction');

				// Add all the orders we are deleting to the transaction
				foreach($orders as $order) {
					$transaction->addObject($order);
				}

				// Delete all the orders
				$transaction->delete();

				// If there is only one order we are deleting
				if($newestId == $oldestId) {
					$this->_getSession()->addSuccess("Successfully deleted order number {$newestId}.");
				} else {
					$this->_getSession()->addSuccess("Successfully deleted order numbers {$oldestId}-{$newestId}.");
				}
			} catch(Exception $e) {
				$lastAttemptedOrder = Mage::registry('__amx_orderdelete_last_attempted_order');

				if($lastAttemptedOrder) {
					$this->_getSession()->addError("An error occured when deleting order number {$lastAttemptedOrder->getIncrementId()}!");
				} else {
					$this->_getSession()->addError('An error occured when deleting an order!');
				}
			}
		} else {
			$this->_getSession()->addError('Could not load orders!');
		}
	}

	public function massactionAction() {
		$orderIds = $this->getRequest()->getParam('order_ids');

		$this->delete($orderIds);

		$this->_redirect('adminhtml/sales_order');
	}

	public function deleteAction() {
		$orderId = $this->getRequest()->getParam('order_id');

		$this->delete($orderId);

		$this->_redirect('adminhtml/sales_order');
	}
}