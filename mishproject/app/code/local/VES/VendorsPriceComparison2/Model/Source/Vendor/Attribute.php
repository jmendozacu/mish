<?php

class VES_VendorsPriceComparison2_Model_Source_Vendor_Attribute extends Varien_Object
{
    static protected function _getVendorAttributeCollection(){
        $vendorAttributeType = Mage::getResourceModel('eav/entity_type_collection')->addFieldToFilter('entity_type_code','ves_vendor')->getFirstItem();
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->addFieldToFilter('entity_type_id',$vendorAttributeType->getId())
        ;
        return $collection;
    }
    
    static public function getOptionArray()
    {
    	$result = array(
    	    VES_VendorsPriceComparison2_Model_Pricecomparison::CONDITION_NEW => Mage::helper('pricecomparison2')->__('New'),
    	    VES_VendorsPriceComparison2_Model_Pricecomparison::CONDITION_USED => Mage::helper('pricecomparison2')->__('Used'),
    	);
    	return $result;
    }
    
	static public function toOptionArray()
    {
        $result = array();
        $vendorAttributeCollection = self::_getVendorAttributeCollection();
        foreach($vendorAttributeCollection as $attribute){
            $label = $attribute->getData('frontend_label')?$attribute->getData('frontend_label'):$attribute->getData('attribute_code');
            $result[] = array('label'=>$label,'value'=>$attribute->getData('attribute_code'));
        }
    	
    	return $result;
    }

}