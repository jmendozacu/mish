<?php

class VES_VendorsQuote_Model_Resource_Quote extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsquote/quote', 'quote_id');
    }
    
    /**
     * Load quote data by customer identifier and vendor id
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     * * @param int $vendorId
     * @return Mage_Sales_Model_Resource_Quote
     */
    public function loadQuoteByCustomerId($quote, $customerId,$vendorId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $this->_getLoadSelect('customer_id', $customerId, $quote)
        ->where('vendor_id=?', $vendorId)
        ->where('status = ?', VES_VendorsQuote_Model_Quote::STATUS_CREATED)
        ->order('updated_at ' . Varien_Db_Select::SQL_DESC)
        ->limit(1);
    
        $data    = $adapter->fetchRow($select);
    
        if ($data) {
            $quote->setData($data);
        }
    
        $this->_afterLoad($quote);
    
        return $this;
    }
    
}