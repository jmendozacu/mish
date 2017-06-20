<?php
class VES_VendorsPriceComparison2_Model_Pricecomparison extends Mage_Core_Model_Abstract
{
    const CONDITION_NEW     = 1;
    const CONDITION_USED    = 2;
    
    const STATUS_PENDING    = 0;
    const STATUS_APPROVED   = 1;
    const STATUS_UNAPPROVED = 2;
    
    protected $_product;
    
    protected $_vendor;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('pricecomparison2/pricecomparison');
    }
    
    /**
     * Get related product object
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct(){
        if(!$this->_product){
            $this->_product = Mage::getModel('catalog/product')->load($this->getProductId());
        }
        return $this->_product;
    }
    
    /**
     * Get Related vendor object
     * @return VES_Vendors_Model_Vendor
     */
    public function getVendor(){
        if(!$this->_vendor){
            $this->_vendor = Mage::getModel('vendors/vendor')->load($this->getVendorId());
        }
        return $this->_vendor;
    }
}