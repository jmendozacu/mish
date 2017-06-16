<?php

/**
 * Catalog product list
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Catalog_Product_List extends VES_VendorsProduct_Block_Catalog_Product_List_Amasty_Pure
{
	protected function _getProductCollection(){
	    /*Do nothing if the extension is disabled*/
	    if(!Mage::helper('vendors')->moduleEnabled()) return parent::_getProductCollection();
	    
		$collection = parent::_getProductCollection();
		$collection->addAttributeToFilter('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED);
		
		$froms = $collection->getSelect()->getPart('from');
		$hasProductEntityTbl = false;
		foreach($froms as $from){
			if(isset($from['tableName']) && ($from['tableName'] == $collection->getTable('catalog/product'))) $hasProductEntityTbl = true;
		}

		if(!$this->getIsFilteredActivatedVendors()){
			if(!$hasProductEntityTbl){
				/*Fix for price filter*/
				$collection->joinTable(array('catalog_tbl'=>'catalog/product'), 'entity_id=entity_id',array('vendor_id'=>'vendor_id'));
			}
			/*Get not activated vendors*/
			$vendorCollection = Mage::getModel('vendors/vendor')->getCollection()->addAttributeToFilter('status',array('neq'=>VES_Vendors_Model_Vendor::STATUS_ACTIVATED));
			$ids = $vendorCollection->getAllIds();
			
			if(sizeof($ids)) $collection->addAttributeToFilter('vendor_id',array('nin'=>$ids));
			$this->setIsFilteredActivatedVendors(true);
		}
		Mage::dispatchEvent('ves_vendorsproduct_product_list_prepare_after',array('collection'=>$collection));
		
		return $collection;
	}
}
