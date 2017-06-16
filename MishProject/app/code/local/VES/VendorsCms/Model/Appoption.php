<?php
/**
 * Vendor CMS App Option model
 *
 * @category   VES
 * @package    VES_VendorsCms
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsCms_Model_Appoption extends Mage_Core_Model_Abstract
{
    const CACHE_TAG              = 'vendor_cms_app_option';
    protected $_cacheTag         = 'vendor_cms_app_option';
    
    /**
     * Prefix of model events names
     *
     * @var string
     */

    protected $_eventPrefix = 'vendor_cms_app_option';
    
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscms/appoption');
    }
}