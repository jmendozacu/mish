<?php
class VES_VendorsQuote_Model_Resource_Quote_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_quote;
    
	protected function _construct()
    {
        $this->_init('vendorsquote/quote_item');
    }
    
    /**
     * Set Quote object to Collection
     *
     * @param VES_VendorsQuote_Model_Quote $quote
     * @return Mage_Sales_Model_Resource_Quote_Item_Collection
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        $quoteId      = $quote->getId();
        if ($quoteId) {
            $this->addFieldToFilter('quote_id', $quote->getId());
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }
        return $this;
    }
}