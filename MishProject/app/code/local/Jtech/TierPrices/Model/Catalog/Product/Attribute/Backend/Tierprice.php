<?php
/************************************************************************
 * 
 * jtechextensions @ J-Tech LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.jtechextensions.com/LICENSE-M1.txt
 *
 * @package    Tiered Pricing By Percent
 * @copyright  Copyright (c) 2012-2013 jtechextensions @ J-Tech LLC. (http://www.jtechextensions.com)
 * @license    http://www.jtechextensions.com/LICENSE-M1.txt
************************************************************************/
class Jtech_TierPrices_Model_Catalog_Product_Attribute_Backend_Tierprice
    extends Mage_Catalog_Model_Product_Attribute_Backend_Tierprice
{
    public function afterSave($object)
    {
        $websiteId  = Mage::app()->getStore($object->getStoreId())->getWebsiteId();
        $isGlobal   = $this->getAttribute()->isScopeGlobal() || $websiteId == 0;

        $priceRows = $object->getData($this->getAttribute()->getName());
        if (empty($priceRows)) {
            if ($isGlobal) {
                $this->_getResource()->deletePriceData($object->getId());
            } else {
                $this->_getResource()->deletePriceData($object->getId(), $websiteId);
            }
            return $this;
        }

        $old = array();
        $new = array();

        // prepare original data for compare
        $origGroupPrices = $object->getOrigData($this->getAttribute()->getName());
        if (!is_array($origGroupPrices)) {
            $origGroupPrices = array();
        }
        foreach ($origGroupPrices as $data) {
            if ($data['website_id'] > 0 || ($data['website_id'] == '0' && $isGlobal)) {
                $key = join('-', array_merge(
                    array($data['website_id'], $data['cust_group']),
                    $this->_getAdditionalUniqueFields($data)
                ));
                $old[$key] = $data;
            }
        }

        // prepare data for save
        foreach ($priceRows as $data) {
            $hasEmptyData = false;
            foreach ($this->_getAdditionalUniqueFields($data) as $field) {
                if (empty($field)) {
                    $hasEmptyData = true;
                    break;
                }
            }

            if ($hasEmptyData || !isset($data['cust_group']) || !empty($data['delete'])) {
                continue;
            }
            if ($this->getAttribute()->isScopeGlobal() && $data['website_id'] > 0) {
                continue;
            }
            if (!$isGlobal && (int)$data['website_id'] == 0) {
                continue;
            }

            $key = join('-', array_merge(
                array($data['website_id'], $data['cust_group']),
                $this->_getAdditionalUniqueFields($data)
            ));

            $useForAllGroups = $data['cust_group'] == Mage_Customer_Model_Group::CUST_GROUP_ALL;
            $customerGroupId = !$useForAllGroups ? $data['cust_group'] : 0;

            $new[$key] = array_merge(array(
                'website_id'        => $data['website_id'],
                'all_groups'        => $useForAllGroups ? 1 : 0,
                'customer_group_id' => $customerGroupId,
                'value'             => $data['price'],
                'value_type'        => $data['value_type']
            ), $this->_getAdditionalUniqueFields($data));
        }

        $delete = array_diff_key($old, $new);
        $insert = array_diff_key($new, $old);
        $update = array_intersect_key($new, $old);

        $isChanged  = false;
        $productId  = $object->getId();

        if (!empty($delete)) {
            foreach ($delete as $data) {
                $this->_getResource()->deletePriceData($productId, null, $data['price_id']);
                $isChanged = true;
            }
        }

        if (!empty($insert)) {
            foreach ($insert as $data) {
                $price = new Varien_Object($data);
                $price->setEntityId($productId);
                $this->_getResource()->savePriceData($price);

                $isChanged = true;
            }
        }

        if (!empty($update)) {
            foreach ($update as $k => $v) {
                if (($old[$k]['price'] != $v['value']) || $old[$k]['value_type'] != $v['value_type']) {
                    $price = new Varien_Object(array(
                        'value_id'      => $old[$k]['price_id'],
                        'value'         => $v['value'],
                        'value_type'    => $v['value_type']
                    ));
                    $this->_getResource()->savePriceData($price);

                    $isChanged = true;
                }
            }
        }

        if ($isChanged) {
            $valueChangedKey = $this->getAttribute()->getName() . '_changed';
            $object->setData($valueChangedKey, 1);
        }

        return $this;
    }
}