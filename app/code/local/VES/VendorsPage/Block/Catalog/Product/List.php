<?php
class VES_VendorsPage_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
	/**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addFieldToFilter('vendor_id',Mage::registry('vendor')->getId())
            ->addAttributeToFilter('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED);;
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        	Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        	$this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }
    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock('vendorspage/catalog_product_list_toolbar', microtime());
        return $block;
    }
	/**
     * Retrieve product amount per row
     *
     * @return int
     */
    public function getColumnCount()
    {
        if (!$this->_getData('column_count')) {
        	$vendorId = Mage::registry('vendor')->getId();
        	$this->setData('column_count', Mage::helper('vendorsconfig')->getVendorConfig('design/home/product_count',$vendorId));
        }

        return (int) $this->_getData('column_count');
    }
}
