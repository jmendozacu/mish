<?php

class VES_VendorsCms_Model_Resource_Page extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorscms/page', 'page_id');
    }
	/**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniquePageToVendor($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key already exists.'));
        }

        if (!$this->isValidPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key cannot consist only of numbers.'));
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
    public function getIsUniquePageToVendor(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('cp' => $this->getMainTable()))
            ->where('cp.identifier = ?', $object->getData('identifier'))
            ->where('cp.vendor_id = ?', $object->getData('vendor_id'))
            ;

        if ($object->getId()) {
            $select->where('cp.page_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }
    
	/**
     *  Check whether page identifier is numeric
     *
     * @date Wed Mar 26 18:12:28 EET 2008
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    protected function isNumericPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether page identifier is valid
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     */
    protected function isValidPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }
    
	/**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $vendorId
     * @return int
     */
    public function checkIdentifier($identifier, $vendorId)
    {
    	$select = $this->_getReadAdapter()->select()
            ->from(array('cp' => $this->getMainTable()))
            ->where('cp.identifier = ?', $identifier)
            ->where('cp.vendor_id = ?', $vendorId)
			->where('cp.is_active = ?', 1);
        
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('cp.page_id')
            ->limit(1);
        return $this->_getReadAdapter()->fetchOne($select);
    }
}