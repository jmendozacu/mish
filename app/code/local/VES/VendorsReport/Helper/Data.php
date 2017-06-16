<?php

class VES_VendorsReport_Helper_Data extends Mage_Core_Helper_Abstract {

    public function moduleEnable() {
        $result = new Varien_Object(array('module_enable' => true));
        Mage::dispatchEvent('ves_vendorsreport_module_enable', array('result' => $result));
        return $result->getData('module_enable');
    }

    //get all warehouse name
    public function getAllWarehouseName() { 
        $warehouses = array();
        $vendorId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $model = Mage::getModel('inventoryplus/warehouse');
        $collection = $model->getCollection()->addFieldToFilter('vendor_id',$vendorId);
        foreach ($collection as $warehouse) {
            $warehouses[$warehouse->getId()] = $warehouse->getWarehouseName();
        }
        return $warehouses;
    }

}
