<?php
/**
 * Vendor CMS App model
 *
 * @category   VES
 * @package    VES_VendorsCms
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsCms_Model_App extends Mage_Core_Model_Abstract
{
    const CACHE_TAG              = 'vendor_cms_app';
    protected $_cacheTag         = 'vendor_cms_app';
    
    /**
     * App options 
     */
    protected $_options;
    
    /**
     * Prefix of model events names
     *
     * @var string
     */

    protected $_eventPrefix = 'vendor_cms_app';
    
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorscms/app');
    }
    
    /**
     * Get app options
     */
    public function getOptions(){
    	if(!$this->_options){
    		$this->_options = Mage::getModel('vendorscms/appoption')->getCollection()->addFieldToFilter('app_id',$this->getId());
    	}
    	return $this->_options;
    }
    
    /**
     * Get options by code
     * @param unknown_type $code
     */
    public function getOptionsByCode($code){
    	$options = array();
    	foreach ($this->getOptions() as $option){
    		if($option->getCode() == $code){
    			$options[] = $option;
    		}
    	}
    	return $options;
    }
    
    public function getFrontendInstanceOption(){
    	return $this->getOptionsByCode('frontend_instance');
    }
}