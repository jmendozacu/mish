<?php

class VES_VendorsPaypal_Model_Observer
{
    /**
     * Replace the default paypal standard by new form when customer use advanced x mode.
     * @param Varien_Event_Observer $observer
     */
	public function predispatch_paypal_standard_redirect(Varien_Event_Observer $observer){
	    if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED_X) return;
	    $orderIds = Mage::getSingleton('checkout/session')->getOrderIds();
	    if(!$orderIds) return;
	    
	    $controllerAction = $observer->getControllerAction();
	    
	    $session = Mage::getSingleton('checkout/session');
	    $session->setPaypalStandardQuoteId($session->getQuoteId());
	    $controllerAction->getResponse()->setBody($controllerAction->getLayout()->createBlock('vendorspaypal/standard_redirect')->toHtml());
	    $session->unsQuoteId();
	    $session->unsRedirectUrl();
	    
	    $controllerAction->setFlag('',Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,1);
	}
	/**
     * When customer cancel the transaction just cancel created orders (advanced x mode)
     * @param Varien_Event_Observer $observer
     */
	public function predispatch_paypal_standard_cancel(Varien_Event_Observer $observer){
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED_X) return;
	    $orderIds = Mage::getSingleton('checkout/session')->getOrderIds();
	    if(!$orderIds) return;
		
		$controllerAction = $observer->getControllerAction();
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getPaypalStandardQuoteId(true));
		
		foreach($orderIds as $id=>$orderId){
			$order = Mage::getModel('sales/order')->load($id);
            if ($order->getId()) {
                $order->cancel()->save();
            }
		}
		/*Restore quote*/
		$quote = Mage::getModel('sales/quote')->load($session->getQuoteId());
		if ($quote->getId()) {
			$quote->setIsActive(1)
				->setReservedOrderId(null)
				->save();
			$session->replaceQuote($quote)
				->unsLastRealOrderId();
		}
		$controllerAction->setFlag('',Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,1);
		$controllerAction->setRedirectWithCookieCheck('checkout/cart');
	}
	
	public function predispatch_paypal_ipn_index(Varien_Event_Observer $observer){
		if(Mage::helper('vendors')->getMode() != VES_Vendors_Model_Vendor::MODE_ADVANCED_X) return;
		$controllerAction = $observer->getControllerAction();
		Mage::log($controllerAction->getRequest()->getParams(),null,'vendorspaypal.log');
		
		$data = $controllerAction->getRequest()->getPost();
		$id = $data['invoice'];

		/*Do nothing if the order id does not have separated character*/
		if(strpos($id,VES_VendorsPaypal_Model_Standard::SEPARATED_CHAR) === false) return;
		Mage::log('- Start custom ipn processing',null,'vendorspaypal.log');
		Mage::getModel('vendorspaypal/ipn')->processIpnRequest($data, new Varien_Http_Adapter_Curl());
		Mage::log('DONE',null,'vendorspaypal.log');
		$controllerAction->setFlag('',Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,1);
	}
}