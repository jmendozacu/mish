<?php

class VES_VendorsQuote_IndexController extends Mage_Core_Controller_Front_Action
{
    
    /**
     * Retrieve quote session model object
     *
     * @return VES_VendorsQuote_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('vendorsquote/session');
    }
    
    /**
     * Retrieve customer session model object.
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession(){
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * Get quote session
     * @return VES_VendorsQuote_Model_Session
     */
    protected function _getQuoteSession(){
        return $this->_getSession();
    }

    /**
     * Action predispatch
     *
     * Check customer authentication
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice
    
        parent::preDispatch();
    
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if(!Mage::helper('vendorsquote')->requireCustomerLogin()) return;
        if (!$this->_getCustomerSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
    
    public function indexAction(){
        $quotes = $this->_getSession()->getQuotes();
        foreach($quotes as $quote){
            $quote->collectTotals()->save();
        }
        $this->loadLayout()
        ->_initLayoutMessages('vendorsquote/session')
        ->_initLayoutMessages('catalog/session')
        ->getLayout()->getBlock('head')->setTitle($this->__('Quote list'));
        $this->renderLayout();
    }
    
    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
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
    
    protected function _goBack(){
        $this->_redirect('*/index');
    }
    public function additemAction(){
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }

        $quoteSession   = $this->_getQuoteSession();
        $params         = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $quoteSession->addProduct($product, $params);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('vendors_quote_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            
            $message = $this->__('%s was added to your quote.', Mage::helper('core')->escapeHtml($product->getName()));
            $this->_getSession()->addSuccess($message);
            
            $this->_redirect('*/index');
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $this->_redirect('*/index');
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to quote.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }
    
    public function removeAction(){
        $id = $this->getRequest()->getParam('id');
        try{
            $item = Mage::getModel('vendorsquote/quote_item')->setId($id)->delete();
            $this->_getSession()->addSuccess($this->__('The item was deleted successfully.'));
            $this->_goBack();
        }catch (Exception $e){
            $this->_getSession()->addException($e, $this->__('Cannot delete the item.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }
    
    public function sendRequestAction(){
        $quoteInfo  = $this->getRequest()->getParam('customer');
        $quoteInfo['client_comment'] = isset($quoteInfo['client_request'])?$quoteInfo['client_request']:'';
        $itemsInfo  = $this->getRequest()->getParam('item',array());
        $quoteId    =  $this->getRequest()->getParam('quote_id');
        try{
            if(!$quoteId) throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not available.'));
            
            $quoteIds = Mage::getSingleton('vendorsquote/session')->getQuoteIds();
            if(!in_array($quoteId,$quoteIds)) throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not available.'));
            
            $quote = Mage::getModel('vendorsquote/quote')->load($quoteId);
            if(!$quote->getId())  throw new Mage_Core_Exception(Mage::helper('vendorsquote')->__('The quote is not available.'));
            
            $quote->addData($quoteInfo)->setStatus(VES_VendorsQuote_Model_Quote::STATUS_PROCESSING);
            foreach($itemsInfo as $itemId=>$itemData){
                $quoteItem = $quote->getItemById($itemId);
                if($quoteItem){
                    $quoteItem->updateRequestedQty(isset($itemData['qty'])?$itemData['qty']:1);
                    $quoteItem->setData('client_comment',isset($itemData['comment'])?$itemData['comment']:'');
                    $quoteItem->save();
                }
            }
            
            /*Update expiration date and reminder date*/
            $helper         = Mage::helper('vendorsquote');
            $expirationTime = $helper->getExpirationTime();
            $reminderTime   = $helper->getReminderTime();
            $now            = Mage::getModel('core/date')->date('Y-m-d');
            
            $quote->setData('expiry_date',date('Y-m-d',strtotime($now." +".$expirationTime." days")));
            $quote->setData('reminder_date',date('Y-m-d',strtotime($now." +".$reminderTime." days")));
            
            $quote->save();
            /*Send new quote request notification email to customer*/
            $helper->sendNewRequestEmail($quote);
            $this->_getSession()->setLastSuccessQuoteId($quote->getId());
            $this->_getSession()->setLastQuoteId($quote->getId());
            $this->_redirect('*/*/success');
        }catch(Exception $e){
            $this->_getSession()->addException($e,$e->getMessage());
            $this->_goBack();
        }
    }
    
    public function successAction(){
        $this->_title(Mage::helper('vendorsquote')->__('Success.'));
        $session = $this->_getSession();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('vquote/index');
            return;
        }
        
        $lastQuoteId = $session->getLastQuoteId();
        $quote = Mage::getModel('vendorsquote/quote')->load($lastQuoteId);
        Mage::register('quote',$quote);
        $session->unsetData('last_success_quote_id');
        $session->unsetData('last_quote_id');
        $session->setQuoteId($quote->getVendorId(),null);
        
        $this->loadLayout();
        $this->_initLayoutMessages('vendorsquote/session');
        $this->renderLayout();
    }
}
