<?php

class Ameronix_OrderDelete_Helper_Xml
	extends Mage_Core_Helper_Abstract {

	public function getDeleteOrderData() {
		/* @var $salesOrder Mage_Sales_Model_Order */
		$salesOrder = Mage::registry('sales_order');

		if($salesOrder->getId()) {
			$url = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/orderdelete/delete', array('order_id' => $salesOrder->getId()));

			return array(
				'label' => 'Delete Order',
				'class' => 'delete',
				'onclick' => 'if(confirm(\'Are you sure?\')) { setLocation(\'' . $url . '\') }'
			);
		}

	}
}