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
class Jtech_TierPrices_Model_Catalog_Resource_Product_Attribute_Backend_Tierprice
    extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
{
    protected function _loadPriceDataColumns($columns)
    {
        $columns = parent::_loadPriceDataColumns($columns);
        $columns['price_qty'] = 'qty';
        $columns['value_type'] = 'value_type';
        return $columns;
    }
        
    public function loadProductPrices($product, $attribute)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this>getMainTable(), array(
                'website_id', 'all_groups', 'cust_group' => 'customer_group_id',
                'price_qty' => 'qty', 'price' => 'value', 'value_type'
            ))
            ->where('entity_id=?', $product->getId())
            ->order('qty');
            
        if ($attribute->isScopeGlobal())
        {
            $select->where('website_id = ?', 0);
        }
        else
        {
            if ($storeId = $product->getStoreId())
            {
                $select->where('website_id IN (?)', array(0, Mage::app()->getStore($storeId)->getWebsiteId()));
            }
        }
        return $this->_getReadAdapter()->fetchAll($select);
    }
}