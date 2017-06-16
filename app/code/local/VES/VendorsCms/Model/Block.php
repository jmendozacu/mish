<?php

class VES_VendorsCms_Model_Block extends Mage_Core_Model_Abstract
{
	const CACHE_TAG     = 'vendor_cms_block';
    protected $_cacheTag= 'vendor_cms_block';
    
    /**
     * Prefix of model events names
     *
     * @var string
     */

    protected $_eventPrefix = 'vendor_cms_block';
    
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscms/block');
    }
    
    /**
     * Load block by identifier
     */
    public function loadBlock($identifier){
    	if(!$this->getVendorId()) Mage::throwException(
            Mage::helper('vendorscms')->__('Vendor ID must be specified.')
        );
        $this->getResource()->loadBlock($identifier,$this);
        return $this;
    }
}