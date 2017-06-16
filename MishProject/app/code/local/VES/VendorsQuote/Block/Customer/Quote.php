<?php

class VES_VendorsQuote_Block_Customer_Quote extends Mage_Core_Block_Template{
    protected $_quotes;
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    
        $pager = $this->getLayout()->createBlock('page/html_pager', 'vendorsquote.list.pager')
        ->setCollection($this->getQuotes());
        $this->setChild('pager', $pager);
        $this->getQuotes()->load();
        return $this;
    }
    
    /**
     * Get customer quote collection
     * @return VES_VendorsQuote_Model_Resource_Quote_Collection
     */
    public function getQuotes(){
        if(!$this->_quotes){
            $this->_quotes = Mage::getModel('vendorsquote/quote')->getCollection()
                ->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId())
                ->addFieldToFilter('status',array('nin'=>array(VES_VendorsQuote_Model_Quote::STATUS_CREATED)))
                ->setOrder('created_at', 'desc');
        }
        
        return $this->_quotes;
    }
    
    
    /**
     * Get quote view url
     * @param VES_VendorsQuote_Model_Quote $quote
     * @return string
     */
    public function getViewUrl(VES_VendorsQuote_Model_Quote $quote){
        return $this->getUrl('customer/quotation/view',array('id'=>$quote->getId()));
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}