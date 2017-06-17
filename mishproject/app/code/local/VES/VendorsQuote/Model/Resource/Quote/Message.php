<?php

class VES_VendorsQuote_Model_Resource_Quote_Message extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorsquote/message', 'message_id');
    }    
}