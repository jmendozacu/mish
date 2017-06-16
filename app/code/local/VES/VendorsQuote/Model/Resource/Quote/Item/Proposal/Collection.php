<?php
class VES_VendorsQuote_Model_Resource_Quote_Item_Proposal_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{    
	protected function _construct()
    {
        $this->_init('vendorsquote/quote_item_proposal');
    }
}