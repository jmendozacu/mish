<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsPriceComparison2_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template
{
    protected $_price_comparison_collection;
    
    /**
     * Get Pending price comparison collection
     * @return VES_VendorsPriceComparison2_Model_Resource_Pricecomparison_Collection
     */
    public function getPendingPriceComparisonCollection(){
        if(!$this->_price_comparison_collection){
            $this->_price_comparison_collection = Mage::getModel('pricecomparison2/pricecomparison')->getCollection()
                ->addFieldToFilter('status',VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_PENDING);
        }
        return $this->_price_comparison_collection;
    }
    
    public function getMessage(){
        $productCount = $this->getPendingProductCount();
        if($productCount == 1)return $this->__('There is %s assigned product awaiting for approval. %sClick Here%s to review them.','<strong>'.$productCount.'</strong>',sprintf('<a href="%s">',$this->getPendingProductsListUrl()),'</a>');
        return $this->__('There are %s assigned products awaiting for approval. %sClick Here%s to review them.','<strong>'.$productCount.'</strong>',sprintf('<a href="%s">',$this->getPendingProductsListUrl()),'</a>');
    }
    
    public function getPendingProductsListUrl(){
        return $this->getUrl('adminhtml/vendors_assignedproducts/index',array('product_filter'=>base64_encode('status='.VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_PENDING)));
    }
    
    /**
     * Get Pending Product Count
     * @return int
     */
    public function getPendingProductCount(){
        return $this->getPendingPriceComparisonCollection()->count();
    }
    
    public function _toHtml(){
        if(!$this->getPendingProductCount()) return '';
        return parent::_toHtml();
    }
}
