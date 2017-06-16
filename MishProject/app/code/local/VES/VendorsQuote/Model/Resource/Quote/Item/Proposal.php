<?php

class VES_VendorsQuote_Model_Resource_Quote_Item_Proposal extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsquote/proposal', 'proposal_id');
    }    
}