<?php

class VES_VendorsQuote_Model_Quote_Message extends Mage_Core_Model_Abstract
{
    const TYPE_VENDOR   = 1;
    const TYPE_CUSTOMER = 2;
    const TYPE_ADMIN    = 3;
    
    protected $_quote;
    
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsquote/quote_message');
    }
    
    /**
     * Processing object before save data
     *
     * @return VES_VendorsQuote_Model_Quote
     */
    protected function _beforeSave()
    {
        if($this->isObjectNew()){
            $this->setData('created_at',Mage::getModel('core/date')->date());
        }
        return parent::_beforeSave();
    }
    
    /**
     * Set Quote
     * @param VES_VendorsQuote_Model_Quote $quote
     */
    public function setQuote(VES_VendorsQuote_Model_Quote $quote){
        $this->_quote = $quote;
        $this->setData('quote_id',$quote->getId());
        return $this;
    }
    
    /**
     * Get quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function getQuote(){
        if(!isset($this->_quote)){
            $this->_quote = Mage::getModel('vendorsquote/quote')->load($this->getQuoteId());
        }
    
        return $this->_quote;
    }
    
    /**
     * get is vendor message
     * @return boolean
     */
    public function isVendorMessage(){
        return $this->getMessageType() == self::TYPE_VENDOR;
    }
    
    /**
     * get is admin essage
     * @return boolean
     */
    public function isAdminMessage(){
        return $this->getMessageType() == self::TYPE_ADMIN;
    }
}