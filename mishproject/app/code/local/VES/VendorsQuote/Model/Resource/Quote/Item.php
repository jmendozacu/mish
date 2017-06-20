<?php

class VES_VendorsQuote_Model_Resource_Quote_Item extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsquote/item', 'item_id');
    }    
}