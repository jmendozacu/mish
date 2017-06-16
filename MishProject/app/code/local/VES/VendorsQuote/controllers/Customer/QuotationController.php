<?php

class VES_VendorsQuote_Customer_QuotationController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get customer session
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession(){
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get quote session
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession(){
        return Mage::getSingleton('checkout/session');
    }
    
    public function indexAction(){
        $this->loadLayout();
        $this->_initLayoutMessages('vendorsquote/session')
        ->_initLayoutMessages('catalog/session')
        ->_initLayoutMessages('checkout/session')
        ->_title($this->__('My Quotes'));
        $this->renderLayout();
    }
    /**
     * Init quote
     * @return boolean|VES_VendorsQuote_Model_Quote
     */
    protected function _initQuote(){
        $quoteIncrementId   = $this->getRequest()->getParam('quote_id','');
        $email              = $this->getRequest()->getParam('email','');
        $quoteId            = $this->getRequest()->getParam('id','');
        $quote              = Mage::getModel('vendorsquote/quote');
        $customerSession    = $this->_getCustomerSession();
        $flag               = true;
        
        if($quoteIncrementId){
            $quote->loadByIncrementId($quoteIncrementId);
            if(!@$email || $quote->getEmail() != $email){
                Mage::getSingleton('core/session')->addError(Mage::helper('vendorsquote')->__('You do not have permission to view this page.'));
                $this->_redirect('');
                return false;
            }
        }elseif($quoteId){
            /*Require logged in customer to view the quote by id*/
            if(!$customerSession->isLoggedIn()){
                $customerSession->setBeforeAuthUrl(Mage::getUrl('customer/quotation/view',array('id'=>$quoteId)));
                $this->_redirect('customer/account/login');
                return false;
            }
            $customer = $customerSession->getCustomer();
        
            $quote->load($quoteId);
            /*If the customer id and email are different with current customer id, email just return error message*/
            if($customer->getId() != $quote->getCustomerId() && $customer->getEmail() != $quote->getEmail()) $flag = false;
        }
        
        
        if(!$quote->getId() || !$flag){
            if($customerSession->isLoggedIn()){
                $customerSession->addError(Mage::helper('vendorsquote')->__('Quote is not exist.'));
                $this->_redirect('customer/account');
            }else{
                Mage::getSingleton('core/session')->addError(Mage::helper('vendorsquote')->__('Quote is not exist.'));
                $this->_redirect('');
            }
            return false;
        }
        
        if(in_array($quote->getStatus(),array(VES_VendorsQuote_Model_Quote::STATUS_CREATED,VES_VendorsQuote_Model_Quote::STATUS_CREATED_NOT_SENT))){
            if($customerSession->isLoggedIn()){
                $customerSession->addError(Mage::helper('vendorsquote')->__('Quote is not available.'));
                $this->_redirect('customer/quotation');
            }else{
                Mage::getSingleton('core/session')->addError(Mage::helper('vendorsquote')->__('Quote is not available.'));
                $this->_redirect('');
            }
            return false;
        }
        return $quote;
    }
    public function guestAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    public function viewAction(){
        $customerSession    = $this->_getCustomerSession();
        $quote = $this->_initQuote();
        if(!$quote) return;
        Mage::register('quote', $quote);
        Mage::register('current_quote', $quote);
        
        if($customerSession->isLoggedIn()){
            $this->loadLayout(array('default','customer_quotation_view','customer_account'));
        }else{
            $this->loadLayout();
        }
        $this->getLayout()->getBlock('head')->setRobots('NOINDEX, NOFOLLOW');
        $this->_initLayoutMessages('vendorsquote/session')
        ->_initLayoutMessages('catalog/session')
        ->_initLayoutMessages('checkout/session')
        ->_title($this->__('Quote #%s',$quote->getIncrementId()))
        ;
        
        $this->renderLayout();
    }
    
    public function sendMessageAction(){
        $quoteId    = $this->getRequest()->getParam('quote_id','');
        $message    = $this->getRequest()->getParam('message','');
        $email      = $this->getRequest()->getParam('email','');
        $session    = $this->_getCustomerSession();
        try{
            $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
            $flag  = false;
            if($session->isLoggedIn()){
                $flag = ($quote->getCustomerId() != $session->getCustomerId()) && ($quote->getEmail() != $session->getCustomer()->getEmail());
            }else {
                $flag = !$email || ($email != $quote->getEmail());
            }
            if(!$quoteId || !$quote->getId() || $flag)
                throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not exist'));
    
            Mage::register('quote', $quote);
            Mage::register('current_quote', $quote);
    
            $message = $quote->addMessage($quote->getCustomerName(), $message,VES_VendorsQuote_Model_Quote_Message::TYPE_CUSTOMER);
            Mage::helper('vendorsquote')->sendQuoteNotificationMessageToVendor($message, $quote);
            
            $messageListBlock = $this->getLayout()->createBlock('vendorsquote/customer_quote_message','message_list')
            ->setTemplate('ves_vendorsquote/customer/message.phtml');
            $result = array('success'=>true,'message_list'=>$messageListBlock->toHtml());
        }catch (Exception $e){
            $result = array('success'=>false,'msg'=>$e->getMessage());
        }
         
        $this->getResponse()->setBody(json_encode($result));
    }
    
    public function saveDefaultProposalAction(){
        $quoteItemId   = $this->getRequest()->getParam('quote_item_id','');
        $proposalId    = $this->getRequest()->getParam('proposal_id',null);
        try{
            $item = Mage::getModel('vendorsquote/quote_item')->load($quoteItemId);
            $item->setData('default_proposal',$proposalId)->save();
            $result = array('success'=>true);
        }catch (Exception $e){
            $result = array('success'=>false,'msg'=>$e->getMessage());
        }
        
        $this->getResponse()->setBody(json_encode($result));
    }
    
    /**
     * Get Quote params.
     * @param VES_VendorsQuote_Model_Quote $quote
     * @return array();
     */
    protected function _getQuoteParams($quote){
        if($this->_getCustomerSession()->isLoggedIn()){
            $params = array('id'=>$quote->getId());
        }else{
            $params = array('quote_id'=>$quote->getIncrementId(),'email'=>$quote->getEmail());
        }
    
        return $params;
    }
    
    public function rejectProposalAction(){
        $quote = $this->_initQuote();
        if(!$quote) return;
        $quote->reject();
        Mage::helper('vendorsquote')->sendRejectProposalNotificationEmail($quote);
        
        Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsquote')->__('Quote #%s was rejected successfully.',$quote->getIncrementId()));
        $params = $this->_getQuoteParams($quote);
        $this->_redirect('customer/quotation/view',$params);
    }
    
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct($params)
    {
        $productId = (int) $params['product'];
        if ($productId) {
            $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
    
    protected function _goBack($quote){
        $params = $this->_getQuoteParams($quote);
        $this->_redirect('customer/quotation/view',$params);
    }
    
    public function confirmAction(){
        $quote = $this->_initQuote();
        if(!$quote) return;

        /*Add quote items to shopping cart*/
        $cart   = $this->_getCart();
        try {
            foreach($quote->getItemsCollection() as $item){
                $defaultProposal = $item->getDefaultProposalObject();
                $params = json_decode($item->getData('buy_request'),true);
                $params['qty'] = $defaultProposal->getQty();
                
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $product = $this->_initProduct($params);
            
                /**
                 * Check product availability
                */
                if (!$product) {
                    $this->_goBack($quote);
                    return;
                }
                
                $product->addCustomOption('related_quote_item',$item->getId());
                $product->setPrice($defaultProposal->getPrice());
                $params['custom_price'] = $defaultProposal->getPrice();
                $cart->addProduct($product, $params);
                /**
                 * @todo remove wishlist observer processAddToCart
                */
                Mage::dispatchEvent('checkout_cart_add_product_complete',
                    array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
            }
            $cart->save();
            $quote->setStatus(VES_VendorsQuote_Model_Quote::STATUS_ACCEPTED)->save();
            $this->_getSession()->setCartWasUpdated(true);
            $this->_redirect('checkout/cart');
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.<br />%s',$e->getMessage()));
            Mage::logException($e);
            $this->_goBack($quote);
        }
    }
}
