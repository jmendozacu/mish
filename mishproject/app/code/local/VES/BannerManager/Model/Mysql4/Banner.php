<?php

class VES_BannerManager_Model_Mysql4_Banner extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the bannermanager_id refers to the key field in your database table.
        $this->_init('bannermanager/banner', 'banner_id');
    }
    
	protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
    	$condition = $this->_getWriteAdapter()->quoteInto('banner_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('banner_store'), $condition);
    }
    
	protected function _afterSave(Mage_Core_Model_Abstract $object){
		$condition = $this->_getWriteAdapter()->quoteInto('banner_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('banner_store'), $condition);

		if (!$object->getData('stores')){
			$storeArray = array();
            $storeArray['banner_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('banner_store'), $storeArray);
		}else{
			foreach ((array)$object->getData('stores') as $store) {
				$storeArray = array();
				$storeArray['banner_id'] = $object->getId();
				$storeArray['store_id'] = $store;
				$this->_getWriteAdapter()->insert($this->getTable('banner_store'), $storeArray);
			}
		}

        return parent::_afterSave($object);
    }
}