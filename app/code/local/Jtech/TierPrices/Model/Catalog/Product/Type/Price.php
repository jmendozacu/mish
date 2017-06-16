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
class Jtech_TierPrices_Model_Catalog_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    public function getTierPrice($qty = null, $product)
    {
        $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData('tier_price');

        if (is_null($prices))
        {
            $attribute = $product->getResource()->getAttribute('tier_price');
            if ($attribute)
            {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData('tier_price');
            }
        }

        if (is_null($prices) || !is_array($prices))
        {
            if (!is_null($qty))
            {
                return $product->getPrice();
            }
            return array(array(
                'price'         => $product->getPrice(),
                'website_price' => $product->getPrice(),
                'price_qty'     => 1,
                'cust_group'    => $allGroups,
            ));
        }

        $custGroup = $this->_getCustomerGroupId($product);
        if ($qty)
        {
            $prevQty = 1;
            $prevPrice = $product->getPrice();
            $prevGroup = $allGroups;

            foreach ($prices as $price)
            {
                if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroups)
                {
                    // tier not for current customer group nor is for all groups
                    continue;
                }
                if ($qty < $price['price_qty'])
                {
                    // tier is higher than product qty
                    continue;
                }
                if ($price['price_qty'] < $prevQty)
                {
                    // higher tier qty already found
                    continue;
                }
                if ($price['price_qty'] == $prevQty && $prevGroup != $allGroups && $price['cust_group'] == $allGroups)
                {
                    // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                    continue;
                }
                
                if ($price['value_type'] == 'discount_percent')
                {
                    $price['website_price'] = $product->getPrice() - (($price['website_price'] * $product->getPrice()) / 100); 
                }
                if ($price['website_price'] < $prevPrice)
                {
                    $prevPrice  = $price['website_price'];
                    $prevQty    = $price['price_qty'];
                    $prevGroup  = $price['cust_group'];
                }
            }
            return $prevPrice;
        }
        else
        {
            $qtyCache = array();
            foreach ($prices as $i => $price)
            {
                if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroups)
                {
                    unset($prices[$i]);
                    continue;
                }
                else if (isset($qtyCache[$price['price_qty']]))
                {
                    $j = $qtyCache[$price['price_qty']];
                    if ($prices[$j]['website_price'] > $price['website_price']) {
                        unset($prices[$j]);
                        $qtyCache[$price['price_qty']] = $i;
                    } else {
                        unset($prices[$i]);
                    }
                }
                else
                {
                    $qtyCache[$price['price_qty']] = $i;
                }
                
                if ($price['value_type'] == 'discount_percent')
                {
                    $price = $product->getPrice() - (($price['website_price'] * $product->getPrice()) / 100);
                    $prices[$i]['website_price'] = $price;
                    $prices[$i]['price'] = $price;
                }
            }
        }

        return ($prices) ? $prices : array();
    }
}