<?php

class VES_VendorsCms_Model_Page extends Mage_Core_Model_Abstract
{
	/**
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    const CACHE_TAG              = 'vendor_cms_page';
    protected $_cacheTag         = 'vendor_cms_page';
    
    /**
     * Prefix of model events names
     *
     * @var string
     */

    protected $_eventPrefix = 'vendor_cms_page';
    
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscms/page');
    }
    
	/**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('cms')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('cms')->__('Disabled'),
        ));

        Mage::dispatchEvent('vendor_cms_page_get_available_statuses', array('statuses' => $statuses));

        return $statuses->getData();
    }
    
	/**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $vendorId
     * @return int
     */
    public function checkIdentifier($identifier,$vendorId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $vendorId);
    }
}