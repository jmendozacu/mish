<?php

class VES_VendorsCms_Model_Resource_Block extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorscms/block', 'block_id');
    }
	/**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniqueBlockToVendor($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key already exists.'));
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }
	/**
     * Check for unique of identifier of page to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueBlockToVendor(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('cp' => $this->getMainTable()))
            ->where('cp.identifier = ?', $object->getData('identifier'))
            ;

        if ($object->getId()) {
            $select->where('cp.block_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }
    
    public function loadBlock($identifier, VES_VendorsCms_Model_Block $block){
    	$select = $this->_getReadAdapter()->select()
            ->from(array('cp' => $this->getMainTable()))
            ->where('cp.identifier = ?', $identifier)
            ->where('cp.vendor_id = ?', $block->getVendorId())
            ;
       	$result = $this->_getWriteAdapter()->fetchRow($select);
        $block->setData($result);
        return $this;
    }
}