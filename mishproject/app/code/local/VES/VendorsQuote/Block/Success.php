<?php

class VES_VendorsQuote_Block_Success extends Mage_Core_Block_Template{
    protected $_quote;

    /**
     * Get Quote session
     * @return VES_VendorsQuote_Model_Session
     */
    protected function _getSession(){
        return Mage::getSingleton('vendorsquote/session');
    }
    
    /**
     * Get quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function getQuote(){
        if(!$this->_quote){
            $this->_quote = Mage::registry('quote');
        }
        return $this->_quote;
    }
    
    /**
     * Get quote increment id
     */
    public function getQuoteId(){
        return $this->getQuote()->getIncrementId();
    }
    /**
     * Get view quote url
     * @return Ambigous <string, mixed>
     */
    public function getViewQuoteUrl(){
        return $this->getUrl('customer/quotation/view',array('id'=>$this->getQuote()->getId()));
    }
    
    /**
     * Get view guest quote url
     * @return Ambigous <string, mixed>
     */
    public function getViewQuestQuoteUrl(){
        return $this->getUrl('customer/quotation/view',array('quote_id'=>$this->getQuote()->getIncrementId(),'email'=>$this->getQuote()->getEmail()));
    }
    /**
     * Only logged in customer can view quote.
     */
    public function getCanViewQuote(){
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
}