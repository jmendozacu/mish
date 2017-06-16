<?php
class VES_VendorsCategory_Block_Product_List extends Mage_Catalog_Block_Product_List
{
	/**
     * Retrieve loaded category collection
     *get collection for paginatinon
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
        	$current_cat 	= Mage::registry('current_vendor_category');
           	$collection 	= Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addFieldToFilter('vendor_id',Mage::registry('vendor')->getId())
            ->addAttributeToFilter('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED)
            ->addAttributeToFilter('vendor_categories',array('finset'=>$current_cat->getId()))
            ;
            
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
        $block = $this->getLayout()->createBlock('catalog/product_list_toolbar', microtime());
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
    		$pageLayout = $this->getPageLayout();
    		if ($pageLayout) {
    			$this->setData(
    					'column_count',
    					$this->getColumnCountLayoutDepend($pageLayout)
    			);
    		} else {
    			$this->setData('column_count', $this->_defaultColumnCount);
    		}
    	}
    
    	return (int) $this->_getData('column_count');
    }
    
    public function getPageLayout() {
    	$layout = Mage::registry('current_vendor_category')->getPageLayout();
    	switch ($layout) {
    		case '1column': 			$true_layout = 'one_column'; break;
    		case '2columns-left': 		$true_layout = 'two_columns_left'; break;
    		case '2columns-right': 		$true_layout = 'two_columns_right'; break;
    		case '3columns': 			$true_layout = 'three_columns'; break;
    	}
    	
    	return $true_layout;
    }
}
