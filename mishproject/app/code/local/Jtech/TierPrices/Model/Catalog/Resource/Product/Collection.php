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
class Jtech_TierPrices_Model_Catalog_Resource_Product_Collection
    extends Mage_Catalog_Model_Resource_Product_Collection
{
    public function addTierPriceData()
    {
        if ($this->getFlag('tier_price_added')) {
            return $this;
        }

        $tierPrices = array();
        $productIds = array();
        foreach ($this->getItems() as $item) {
            $productIds[] = $item->getId();
            $tierPrices[$item->getId()] = array();
        }
        if (!$productIds) {
            return $this;
        }

        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->getAttribute('tier_price');
        if ($attribute->isScopeGlobal()) {
            $websiteId = 0;
        } else if ($this->getStoreId()) {
            $websiteId = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
        }

        $adapter   = $this->getConnection();
        $columns   = array(
            'price_id'      => 'value_id',
            'website_id'    => 'website_id',
            'all_groups'    => 'all_groups',
            'cust_group'    => 'customer_group_id',
            'price_qty'     => 'qty',
            'price'         => 'value',
            'value_type'    => 'value_type',
            'product_id'    => 'entity_id'
        );
        $select  = $adapter->select()
            ->from($this->getTable('catalog/product_attribute_tier_price'), $columns)
            ->where('entity_id IN(?)', $productIds)
            ->order(array('entity_id','qty'));

        if ($websiteId == '0') {
            $select->where('website_id = ?', $websiteId);
        } else {
            $select->where('website_id IN(?)', array('0', $websiteId));
        }

        foreach ($adapter->fetchAll($select) as $row) {
            $tierPrices[$row['product_id']][] = array(
                'website_id'    => $row['website_id'],
                'cust_group'    => $row['all_groups'] ? Mage_Customer_Model_Group::CUST_GROUP_ALL : $row['cust_group'],
                'price_qty'     => $row['price_qty'],
                'price'         => $row['price'],
                'website_price' => $row['price'],
                'value_type'    => $row['value_type']
            );
        }

        /* @var $backend Mage_Catalog_Model_Product_Attribute_Backend_Tierprice */
        $backend = $attribute->getBackend();

        foreach ($this->getItems() as $item) {
            $data = $tierPrices[$item->getId()];
            if (!empty($data) && $websiteId) {
                $data = $backend->preparePriceData($data, $item->getTypeId(), $websiteId);
            }
            $item->setData('tier_price', $data);
        }

        $this->setFlag('tier_price_added', true);
        return $this;
    }
}