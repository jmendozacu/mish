<?php

class VES_VendorsQuote_Block_Customer_Quote_View extends Mage_Core_Block_Template{
    protected $_quote;
    
    /**
     * Get customer session
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession(){
        return Mage::getSingleton('customer/session');
    }
    /**
     * Get current quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function getQuote(){
        if(!$this->_quote){
            $this->_quote = Mage::registry('quote');
        }
        return $this->_quote;
    }
    
    /**
     * Get back url
     * @return string $url
     */
    public function getQuotesListUrl(){
        return $this->getUrl('customer/quotation');
    }
    
    /**
     * Get show field config
     */
    function showField($field){
        return Mage::helper('vendorsquote')->getConfig($field);
    }
    
    /**
     * Get send quote message url.
     * @return string
     */
    public function getSendQuoteMessageUrl(){
        return $this->getUrl('customer/quotation/sendMessage');
    }
    
    /**
     * Get save default proposal url.
     * @return string
     */
    public function getSaveDefaultProposalUrl(){
        return $this->getUrl('customer/quotation/saveDefaultProposal');
    }
    
    /**
     * Get email info from url params
     * @return string
     */
    public function getEmailParam(){
        return $this->getRequest()->getParam('email','');
    }
    
    /**
     * Can confirm/reject the quote
     * @return boolean
     */
    public function canConfirm(){
        return $this->getQuote()->getStatus() == VES_VendorsQuote_Model_Quote::STATUS_SENT;
    }
    
    /**
     * Get Quote params.
     * @return array();
     */
    protected function _getQuoteParams(){
        if($this->getCustomerSession()->isLoggedIn()){
            $params = array('id'=>$this->getQuote()->getId());
        }else{
            $params = array('quote_id'=>$this->getQuote()->getIncrementId(),'email'=>$this->getQuote()->getEmail());
        }
        
        return $params;
    }
    
    /**
     * Get reject proposal url
     * @return string
     */
    public function getRejectProposalUrl(){
        $params = $this->_getQuoteParams(); 
        return $this->getUrl('customer/quotation/rejectProposal',$params);
    }
    
    /**
     * Get confirm url
     * @return string
     */
    public function getConfirmUrl(){
        $params = $this->_getQuoteParams(); 
        return $this->getUrl('customer/quotation/confirm',$params);
    }
}