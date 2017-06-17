<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventorypurchasing
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorypurchasing Helper
 * 
 * @category    Magestore
 * @package     Magestore_Inventorypurchasing
 * @author      Magestore Developer
 */
class Magestore_Inventorypurchasing_Helper_Supplier extends Mage_Core_Helper_Abstract {

    public function importProduct($data) {
        if (count($data)) {
            Mage::getModel('admin/session')->setData('supplier_product_import', $data);
        }
    }

    public function getAllSupplierName() {
        $suppliers = array();
        $model = Mage::getModel('inventorypurchasing/supplier');
        $collection = $model->getCollection();
        foreach ($collection as $supplier) {
            $suppliers[$supplier->getId()] = $supplier->getSupplierName();
        }
        return $suppliers;
    }

    /**
     * get all suppliers have product by product id
     */
    public function getSupplierByProductId($productId) {
        $supplierProducts = Mage::getModel('inventorypurchasing/supplier_product')
                ->getCollection()
                ->addFieldToFilter('product_id', $productId);
        if (count($supplierProducts)) {
            return $supplierProducts;
        } else {
            return null;
        }
    }

    public function returnArrAllRowOfcolumnOftableSupplier($column) {
        $arr = array();
        $model = Mage::getModel('inventorypurchasing/supplier');
        $collection = $model->getCollection();
        $collection->addFieldToFilter('supplier_status', 1);
        foreach ($collection as $c) {
            $arr[$c->supplier_id] = $c->$column;
        }
        return $arr;
    }

    public function getSupplierInfoBySupplierId($supplierId) {
        $supplierCollection = Mage::getResourceModel('inventorypurchasing/supplier_collection')
                ->addFieldToFilter('supplier_id', $supplierId);
        $data = $supplierCollection->setPageSize(1)->setCurPage(1)->getFirstItem()->getData();

        $link = Mage::helper("adminhtml")->getUrl('adminhtml/inpu_supplier/edit', array('id' => $supplierId));
        $supplierField = "<a href=$link style='color:blue'>" . $data['supplier_name'] . "</a>";
        $supplierField .= "<br/>" . $this->__('Address: ') . $data['street'];
        $supplierField .= "<br/>" . $data['city'] . ',';
        if (!$data['state']) {
            if ($data['state_id']) {
                $state = Mage::getModel('directory/region')->load($data['state_id']);
                $data['state'] = $state->getName();
            }
        }
        if ($data['state']) {
            $supplierField .= " " . $data['state'];
        }
        $supplierField .= ", " . $data['postcode'];
        $country = Mage::getModel('directory/country')->loadByCode($data['country_id']);
        $supplierField .= "<br/>" . $country->getName() . '.';
        $supplierField .= "<br/>" . $this->__('Telephone: ') . $data['telephone'];
        $supplierField .= "<br/>" . $this->__('Email: ') . $data['supplier_email'];
        return $supplierField;
    }

    public function getBillingAddressBySupplierId($supplierId) {
        $supplierCollection = Mage::getResourceModel('inventorypurchasing/supplier_collection')
                ->addFieldToFilter('supplier_id', $supplierId);
        $data = $supplierCollection->setPageSize(1)->setCurPage(1)->getFirstItem()->getData();
        $countryLists = Mage::getModel('directory/country')->getResourceCollection()->loadByStore()->toOptionArray(true);
        $countryList = array();
        foreach ($countryLists as $county) {
            $countryList[$county['value']] = $county['label'];
        }
        $supplierField = $data['supplier_name'];
        $supplierField .= "<br/>" . $data['street'];
        $supplierField .= "<br/>" . $data['city'];
        if (isset($data['state'])) {
            $supplierField .= ", " . $data['state'];
        }
        $supplierField .= ", " . $data['postcode'];
        $supplierField .= "<br/>" . $countryList[$data['country_id']];
        $supplierField .= "<br/>" . $this->__('T: ') . $data['telephone'];
        return $supplierField;
    }

    public function getLast30DaysTime() {
        $start_day = date('Y-m-d 00:00:00');
        $time = 30;
        $from = strftime('%Y-%m-%d 00:00:00', strtotime(date("Y-m-d", strtotime($start_day)) . " -$time day"));
        return $from;
    }
    
    /**
     * Get product ids from supplier ids
     * 
     * @param array $supplierIds
     * @return array
     */
    public function getProductIdsFromSuppliers($supplierIds) {
        $productIds = array();
        $supplierProducts = Mage::getModel('inventorypurchasing/supplier_product')
                                        ->getCollection()
                                        ->addFieldToFilter('supplier_id', array('in' => $supplierIds));
        if(count($supplierProducts)) {
            foreach($supplierProducts as $supplierProduct) {
                $productIds[$supplierProduct->getProductId()] = $supplierProduct->getProductId();
            }
        }
        return $productIds;
    }

}
