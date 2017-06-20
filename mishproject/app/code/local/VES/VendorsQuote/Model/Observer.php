<?php

class VES_VendorsQuote_Model_Observer
{	
	/**
	 * Load customer quote when customer login
	 * @param Varien_Event_Observer $observer
	 */
	public function loadCustomerQuote(Varien_Event_Observer $observer){
	   try {
            Mage::getSingleton('vendorsquote/session')->loadCustomerQuote();
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('vendorsquote/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('vendorsquote/session')->addException(
                $e,
                Mage::helper('checkout')->__('Load customer quote error')
            );
        }
	}
	
	/**
	 * Clear customer quote session when customer logout.
	 */
	public function clearQuoteSession(){
	    Mage::getSingleton('vendorsquote/session')->unsetAll();
	}
	/**
	 * Do not allow to update the qty/info of the confirmed quote item
	 * @param Varien_Event_Observer $observer
	 */
	public function predispatch_checkout_cart_updatePost(Varien_Event_Observer $observer){
	    $action = $observer->getControllerAction();
	    $updateAction = (string)$action->getRequest()->getParam('update_cart_action');
	    if($updateAction == 'empty_cart') return;
	    $checkoutSession = Mage::getSingleton('checkout/session');
	    $quote = $checkoutSession->getQuote();
	    $cartData = $action->getRequest()->getParam('cart');
	    if (is_array($cartData)) {
	        foreach ($cartData as $index => $data) {
	            $item = $quote->getItemById($index);
	            if(!$item || !$item->getId()) continue;
	             
	            $option = $item->getOptionByCode('related_quote_item');
	            if(!$option || !$option->getValue()) continue;
	            $checkoutSession->addError(Mage::helper('vendorsquote')->__('Item <strong>%s</strong> - %s is not allowed to update qty.',$item->getName(),Mage::helper('checkout')->formatPrice($item->getPrice())));
	            unset($cartData[$index]);
	        }
	        $action->getRequest()->setParam('cart', $cartData);
	    }
	}
	
	/**
	 * Do not allow to edit the confirmed quote item.
	 * @param Varien_Event_Observer $observer
	 */
	public function predispatch_checkout_cart_configure(Varien_Event_Observer $observer){
	    $action = $observer->getControllerAction();
	    $id = (int) $action->getRequest()->getParam('id');
	    if(!$id) return;
	     
	    $item = Mage::getSingleton('checkout/session')->getQuote()->getItemById($id);
	    if(!$item)return;
	    $option = $item->getOptionByCode('related_quote_item');
	    if(!$option || !$option->getValue()) return;
	    Mage::getSingleton('checkout/session')->addError(Mage::helper('vendorsquote')->__('This item is not allowed to edit.'));
	    $action->setRedirectWithCookieCheck('checkout/cart');
	    $action->setFlag('',Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,1);
	}
	
	/**
	 * Do not allow to edit confirmed quote item
	 * @param Varien_Event_Observer $observer
	 */
	public function predispatch_checkout_cart_ajaxUpdate(Varien_Event_Observer $observer){
	    $action = $observer->getControllerAction();
	    $id = (int) $action->getRequest()->getParam('id');
	    if(!$id) return;
	     
	    $item = Mage::getSingleton('checkout/session')->getQuote()->getItemById($id);
	    if(!$item)return;
	    $option = $item->getOptionByCode('related_quote_item');
	    if(!$option || !$option->getValue()) return;
	     
	    $result=array(
	        'success'=>0,
	        'error'=>Mage::helper('vendorsquote')->__('Item %s - %s is not allowed to update qty.',$item->getName(),Mage::app()->getStore()->formatPrice($item->getPrice(),false))
	    );
	
	    $action->getResponse()->setHeader('Content-type', 'application/json');
	    $action->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	    $action->setFlag('',Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,1);
	}
	/**
	 * set the item custom price.
	 * @param Varien_Event_Observer $observer
	 */
	public function sales_quote_item_set_product(Varien_Event_Observer $observer){
	    $quoteItem = $observer->getQuoteItem();
	    $product   = $observer->getProduct();
	    $quotationOption  = $product->getCustomOption('related_quote_item');
	    if(!$quotationOption || !$quotationOption->getValue()) return;
	    
	    $quotationItem = Mage::getModel('vendorsquote/quote_item')->load($quotationOption->getValue());
	    if(!$quotationItem->getId()) return;
	    $proposal = $quotationItem->getDefaultProposalObject();
	    $quoteItem->setOriginalCustomPrice($proposal->getPrice());
	    $quoteItem->setCustomPrice($proposal->getPrice());
	}
	
	
	/**
	 * Update custom price of quote item
	 * @param Varien_Event_Observer $observer
	 */
	public function sales_convert_quote_item_to_order_item(Varien_Event_Observer $observer){
	    $orderItem = $observer->getOrderItem();
	    $quoteItem = $observer->getItem();
	    $orderItem->setPrice($quoteItem->getPrice());
	    $orderItem->setBasePrice($quoteItem->getBasePrice());
	}
	
	/**
	 * Update quote status to ordered one customer place the order
	 * @param Varien_Event_Observer $observer
	 */
	public function checkout_submit_all_after(Varien_Event_Observer $observer){
	    $quote = $observer->getQuote();

        $quotations = array();
        foreach($quote->getAllItems() as $item){
            $option = $item->getOptionByCode('related_quote_item');
            if(!$option || !$option->getValue()) continue;
            $quotationItem = Mage::getModel('vendorsquote/quote_item')->load($option->getValue());
            if(!$quotationItem->getId()) continue;
            $quoteId = $quotationItem->getQuoteId();
            /*Make sure we do not update the status of a quote many times.*/
            if(!isset($quotations[$quoteId])){
                $quotations[$quoteId] = $quotationItem->getQuote();
                $quotationItem->getQuote()->setStatus(VES_VendorsQuote_Model_Quote::STATUS_ORDERED)->save();
            }
        }
	}
	
	/**
	 * Process expired quotes
	 */
	public function processExpiredQuotesCron(){
	    try{
	        $now 		= Mage::getModel('core/date')->timestamp();
	        $quoteCollection = Mage::getModel('vendorsquote/quote')->getCollection()
	           ->addFieldToFilter('expiry_date',array('lt'=>Mage::getModel('core/date')->date('Y-m-d H:i:s',$now)))
	           ->addFieldToFilter('status',array('nin'=>array(
	               VES_VendorsQuote_Model_Quote::STATUS_EXPIRED,
	               VES_VendorsQuote_Model_Quote::STATUS_ORDERED,
	               VES_VendorsQuote_Model_Quote::STATUS_REJECTED,
	               VES_VendorsQuote_Model_Quote::STATUS_CANCELLED,
	           )));
	           
	        foreach($quoteCollection as $quote){
	            $quote->setStatus(VES_VendorsQuote_Model_Quote::STATUS_EXPIRED)->save();
	            Mage::helper('vendorsquote')->sendExpiredQuoteNotificationEmail($quote);
	        }
	        
	        
	        Mage::log('Check expired quote for date'.Mage::getModel('core/date')->date('Y-m-d',$now),Zend_Log::INFO,'ves_vendor_membership.log');
	    }catch(Mage_Core_Exception $e){
	        Mage::log($e->getMessage(),Zend_Log::ERR,'ves_vendor_quote.log');
	    }catch (Exception $e){
	        Mage::log($e->getMessage(),Zend_Log::ERR,'ves_vendor_quote.log');
	    }
	}
	
	/**
	 * Send reminder notification emails 
	 */
	public function sendReminderEmails(){
	    try{
	        $now 		= Mage::getModel('core/date')->timestamp();
	        $quoteCollection = Mage::getModel('vendorsquote/quote')->getCollection()
	        ->addFieldToFilter('reminder_date',array('gt'=>0))
	        ->addFieldToFilter('reminder_date',array('lt'=>Mage::getModel('core/date')->date('Y-m-d H:i:s',$now)))
	        ->addFieldToFilter('sent_reminder_email',0)
	        ->addFieldToFilter('status',array('nin'=>array(
	            VES_VendorsQuote_Model_Quote::STATUS_EXPIRED,
	            VES_VendorsQuote_Model_Quote::STATUS_ORDERED,
	            VES_VendorsQuote_Model_Quote::STATUS_REJECTED,
	            VES_VendorsQuote_Model_Quote::STATUS_CANCELLED,
	            VES_VendorsQuote_Model_Quote::STATUS_CREATED_NOT_SENT,
	        )));

	        foreach($quoteCollection as $quote){
	            $quote->setData('send_reminder_email',1)->save();
	            Mage::helper('vendorsquote')->sendQuoteReminderNotificationEmail($quote);
	        }
	         
	         
	        Mage::log('Check quote reminder for date'.Mage::getModel('core/date')->date('Y-m-d',$now),Zend_Log::INFO,'ves_vendor_membership.log');
	    }catch(Mage_Core_Exception $e){
	        Mage::log($e->getMessage(),Zend_Log::ERR,'ves_vendor_quote.log');
	    }catch (Exception $e){
	        Mage::log($e->getMessage(),Zend_Log::ERR,'ves_vendor_quote.log');
	    }
	}
}