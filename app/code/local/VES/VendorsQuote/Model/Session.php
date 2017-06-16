<?php

class VES_VendorsQuote_Model_Session extends Mage_Core_Model_Session_Abstract
{
	
/**
     * Class constructor. Initialize checkout session namespace
     */
    public function __construct()
    {
        $this->init('vendorsquote');
    }
    
    protected $_quotes;
    
    protected $_quoteIds;
    
    
    protected function _getQuoteIdKey(){
    	return 'vendor_quotation_id_' . Mage::app()->getStore()->getWebsiteId();
    }
    
    /**
     * Set quote ids
     * @param array $quoteIds
     */
    protected function _setQuoteIds($quoteIds = array()){
    	$this->setData($this->_getQuoteIdKey(),serialize($quoteIds));
    }
    
    
    /**
     * Get all vendor quote ids
     */
	public function getQuoteIds(){
    	$quoteIds = $this->getData($this->_getQuoteIdKey());
    	$quoteIds = unserialize($quoteIds);
    	
    	return $quoteIds;
    }
    
    /**
     * Get quote id by vendor id
     * @param int $vendorId
     * @see Mage_Checkout_Model_Session::getQuoteId()
     */
    public function getQuoteId($vendorId){
    	$quoteIds = $this->getQuoteIds();
    	return isset($quoteIds[$vendorId])?$quoteIds[$vendorId]:'';
    }

    
    
    /**
     * Set quote id by vendor id
     * @param int $vendorId
     * @param int $quoteId
     * @see Mage_Checkout_Model_Session::setQuoteId()
     */
    public function setQuoteId($vendorId, $quoteId){
    	/*Add the quote id to quote ids*/
    	$quoteIds = $this->getQuoteIds();
    	if(!$quoteId) unset($quoteIds[$vendorId]);
    	else $quoteIds[$vendorId] = $quoteId;
    	
    	$this->_setQuoteIds($quoteIds);
    }
    
    /**
     * Get all vendor quotes.
     */
    public function getQuotes(){
    	$quoteIds = $this->getQuoteIds();
    	if($quoteIds && is_array($quoteIds) && sizeof($quoteIds)) foreach($quoteIds as $vendorId=>$quoteId){
    		if(!isset($this->_quotes[$vendorId])  && ($quoteId !== null)){
    			$this->_quotes[$vendorId] = $this->getQuote($vendorId);
    		}
    	}
    	
    	return $this->_quotes;
    }
    
	/**
     * Get checkout quote instance by current session
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote($vendorId)
    {    	
        Mage::dispatchEvent('vendorsquote_process_before', array('quote_session' => $this));

        if (!isset($this->_quotes[$vendorId])) {
            /** @var $quote Mage_Sales_Model_Quote */
            $quote = Mage::getModel('vendorsquote/quote')->setStoreId(Mage::app()->getStore()->getId());
        	if ($this->getQuoteId($vendorId)) {
                $quote->load($this->getQuoteId($vendorId));
                
                if (!$quote->getId() || ($quote->getStatus() != VES_VendorsQuote_Model_Quote::STATUS_CREATED)) {
                    $this->setQuoteId($vendorId,null);
                }
            }
            
            $customerSession = Mage::getSingleton('customer/session');

            if (!$this->getQuoteId($vendorId)) {
                if ($customerSession->isLoggedIn() || $this->_customer) {
                    $customer = ($this->_customer) ? $this->_customer : $customerSession->getCustomer();
                    $quote->loadQuoteByCustomer($customer,$vendorId);
                    $this->setQuoteId($vendorId,$quote->getId());
                }
            }

			$quote->setVendorId($vendorId);
			
            if ($customerSession->isLoggedIn() || $this->_customer) {
                $customer = ($this->_customer) ? $this->_customer : $customerSession->getCustomer();
                $quote->setCustomer($customer);
            }

            $quote->setStore(Mage::app()->getStore());
            
            /*Save the quote if it's not saved*/

            if(!$quote->getId()) {
            	$quote->save();
            	$this->setQuoteId($vendorId,$quote->getId());
            }

            $this->_quotes[$vendorId] = $quote;
        }

        return $this->_quotes[$vendorId];
    }
    
    /**
     * Add product to quote.
     * @param Mage_Catalog_Model_Product $product
     * @param unknown $params
     */
    public function addProduct(Mage_Catalog_Model_Product $product, $params = array()){
        $vendorId = $product->getVendorId();
        $quote = $this->getQuote($vendorId);
        $params = new Varien_Object($params);
        $quote->addProduct($product,$params);
        return $this;
    }
    
    /**
     * Load customer quote
     */
    public function loadCustomerQuote(){
        
        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            return $this;
        }

        Mage::dispatchEvent('vendorsquote_load_customer_quote_before', array('quote_session' => $this));

        $customerQuotes = Mage::getModel('vendorsquote/quote')->getCollection()
                        ->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId())
                        ->addFieldToFilter('status',VES_VendorsQuote_Model_Quote::STATUS_CREATED)
                        ;
        $sessionQuoteIds = $this->getQuoteIds();
        if($sessionQuoteIds && is_array($sessionQuoteIds)  &&sizeof($sessionQuoteIds)){
            foreach($this->getQuotes() as $sessionQuote){
                $check = false;
                foreach($customerQuotes as $quote){
                    $vendorId = $quote->getVendorId();
                    if($vendorId == $sessionQuote->getVendorId()){
                        $quote->merge($sessionQuote);
                        $sessionQuote->delete();
                        $check = true;
                    }
                    if($this->getQuoteId($vendorId) != $quote->getId())
                        $this->setQuoteId($vendorId,$quote->getId());
                    
                    if($check) break;
                }
                if(!$check){
                    $sessionQuote->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
                        ->collectTotals()
                        ->save();
                }
            }
        }else{
            foreach($customerQuotes as $quote){
                $this->setQuoteId($vendorId, $quote->getId());
            }
        }
        
        return $this;
    }
    
    public function clear()
    {
        Mage::dispatchEvent('vendor_quote_destroy', array('quote'=>$this->getQuote()));
        $this->_quotes = null;
        $this->setData($this->_getQuoteIdKey(),null);
    }
    
    /**
     * Unset all data associated with object
     */
    public function unsetAll()
    {
        parent::unsetAll();
        $this->_quotes = null;
        $this->_quoteIds = null;
    }
}