<?php

class OTTO_AdvancedFaq_Model_Mysql4_Faq extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the sellerscatalogsearch_id refers to the key field in your database table.
        $this->_init('advancedfaq/faq', 'faq_id');
    }
 
}
