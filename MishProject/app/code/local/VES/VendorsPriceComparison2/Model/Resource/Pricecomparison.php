<?php

class VES_VendorsPriceComparison2_Model_Resource_Pricecomparison extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('pricecomparison2/pricecomparison2', 'entity_id');
    }
    
}