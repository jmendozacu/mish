<?php

class OTTO_AdvancedFaq_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedfaq/category');
    }
    
    public function addStoreFilter($store, $withAdmin = true){
    
    	if ($store instanceof Mage_Core_Model_Store) {
    		$store = array($store->getId());
    	}
    
    	if (!is_array($store)) {
    		$store = array($store);
    	}
		
    	if ($withAdmin) {
    		$store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
    	}
    	var_dump($store);
    	
    	$this->addFieldToFilter('store_id', array('in' =>$store));
    
    	return $this;
    }
    
}