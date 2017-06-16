<?php

 
class VES_VendorsVacation_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function checkInVacation($vendorId) {
    	return true;
        $vacation_status 	= Mage::helper('vendorsconfig')->getVendorConfig('vacation/vacation/vacation_status', $vendorId);
        $date_from 			= Mage::helper('vendorsconfig')->getVendorConfig('vacation/vacation/date_from', $vendorId);
        $date_to 			= Mage::helper('vendorsconfig')->getVendorConfig('vacation/vacation/date_to', $vendorId);
		$today 				= Mage::getModel('core/date')->date('m/d/Y');
        return ($today >= $date_from && $today <= $date_to && $vacation_status == VES_VendorsVacation_Model_Source_Vacation::VACATION_ON);
    }

    public function getProductStatus($vendorId) {
        return Mage::helper('vendorsconfig')->getVendorConfig('vacation/vacation/product_status', $vendorId);
    }

    public function getVacationContent($vendorId) {
        return Mage::helper('vendorsconfig')->getVendorConfig('vacation/vacation/message', $vendorId);
    }

    public function getVacationData() {
        $resource 		= Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $configTable 	= $resource->getTableName('vendorsconfig/config');
		$today 			= Mage::getModel('core/date')->date('m/j/Y');
        $query = "SELECT vendor_id
                  ,MAX(message) as message
                  ,MAX(date_from) as date_from
                  ,MAX(date_to) as date_to
                  ,MAX(product_status) as product_status
                  ,MAX(vacation_status) as vacation_status
                FROM
                (
                  SELECT vendor_id, path, value
                  ,CASE WHEN path like '%vacation/vacation/message%' THEN value END AS message
                  ,CASE WHEN path like '%vacation/vacation/date_from%' THEN value END AS date_from
                  ,CASE WHEN path like '%vacation/vacation/date_to%' THEN value END AS date_to
                ,CASE WHEN path like '%vacation/vacation/product_status%' THEN value END AS product_status
                ,CASE WHEN path like '%vacation/vacation/vacation_status%' THEN value END AS vacation_status

                  FROM ". $configTable ."
                ) AS config

                GROUP BY vendor_id
                having (message is not null and product_status is not null
                and vacation_status is not null and date_from is not null
                and date_to is not null and date_from <= '".$today."' and date_to >= '".$today."')";

        $results = $readConnection->fetchAll($query);
        return $results;
    }

    public function getVacationDataAsCollection() {
        $vacations = $this->getVacationData();
        $collection = new Varien_Data_Collection();
        foreach($vacations as $_vacation) {
            $item = new Varien_Object();
            $item->setData('vendor_id', $_vacation['vendor_id'])
                ->setData('message', $_vacation['message'])
                ->setData('date_from', $_vacation['date_from'])
                ->setData('date_to', $_vacation['date_to'])
                ->setData('product_status', $_vacation['product_status'])
                ->setData('vacation_status', $_vacation['vacation_status']);

            $collection->addItem($item);
        }

        return $collection;
    }

    /**
     * @param $vendor_id
     * @return Varien_Object
     */
    public function getVendorVacationData($vendor_id) {
        $vacations = $this->getVacationData();
        $obj = new Varien_Object();
        foreach($vacations as $vacation) {
            if($vacation['vendor_id'] == $vendor_id) {
                $obj->setData($vacation);
                return $obj;
            }
        }
    }
}