<?php

class VES_BannerManager_Model_Mysql4_Banner_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('bannermanager/banner');
    }
	public function addStoreToFilter($store)
    {
		if (!Mage::app()->isSingleStoreMode()) {
			if ($store instanceof Mage_Core_Model_Store) {
				$store = array($store->getId());
			}
			$this->getSelect()->join(
				array('store_table' => $this->getTable('banner_store')),
				'main_table.banner_id = store_table.banner_id',
				array()
			)
			->where('store_table.store_id in (?)', array(0, $store));

			return $this;
		}
		return $this;
	}
}