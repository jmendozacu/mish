<?php

class VES_VendorsFeaturedProduct_Model_Source_Vendor_ListSort extends Mage_Adminhtml_Model_System_Config_Source_Catalog_ListSort
{

	public function getOptionArray(){
		$options = array();
        $options["position"] = Mage::helper('catalog')->__('Best Value');
   
        foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
            $options[$attribute['attribute_code']] = Mage::helper('catalog')->__($attribute['frontend_label']);
        }
        return $options;
	}
}