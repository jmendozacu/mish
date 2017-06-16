<?php

class VES_VendorsAttributeOptions_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getNotAllowProductAttributes() {
        return array('status', 'tax_class_id' , 'visibility','approval','vendor_categories','ves_vendor_featured','ves_vendor_related_group','ves_enable_comparison','is_billing_vendor');
    }

    public function getConfigAttributes($type = '1') {
        if($type == '1') return explode(',',Mage::getStoreConfig('vendors/vendorsattributeoptions/attributes'));
        return Mage::getStoreConfig('vendors/vendorsattributeoptions/attributes');
    }
}