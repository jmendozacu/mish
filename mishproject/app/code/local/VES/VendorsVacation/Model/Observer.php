<?php

class VES_VendorsVacation_Model_Observer
{


    public function ves_vendorsproduct_product_list_prepare_after(Varien_Event_Observer $ob) {
        //find all vendor in vacation mode, stored it in array
        $vendorIds = array();
        $vacations = Mage::helper('vendorsvacation')->getVacationData();
        foreach($vacations as $vacation) {
        	if($vacation['product_status'] == VES_VendorsVacation_Model_Source_Product::PRODUCT_YES)
            $vendorIds[] = $vacation['vendor_id'];
        }

        $collection = $ob->getCollection();
        if(sizeof($vendorIds)) {
            $collection->addFieldToFilter('vendor_id' , array('nin' => $vendorIds));
        }
    }
}