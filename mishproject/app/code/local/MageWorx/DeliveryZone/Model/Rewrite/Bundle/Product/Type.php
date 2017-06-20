<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_DeliveryZone_Model_Rewrite_Bundle_Product_Type extends Mage_Bundle_Model_Product_Type
{
    /**
     * Return product sku based on sku_type attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getSku($product = null)
    {
        $sku = Mage_Catalog_Model_Product_Type_Abstract::getSku($product);

        if ($this->getProduct($product)->getData('sku_type')) {
            return $sku;
        } else {
            $skuParts = array($sku);

            if ($this->getProduct($product)->hasCustomOptions()) {
                $customOption = $this->getProduct($product)->getCustomOption('bundle_selection_ids');
                if($customOption) {
                    $selectionIds = unserialize($customOption->getValue());
                    if (!empty($selectionIds)) {
                        $selections = $this->getSelectionsByIds($selectionIds, $product);
                        foreach ($selections->getItems() as $selection) {
                            $skuParts[] = $selection->getSku();
                        }
                    }
                }
            }

            return implode('-', $skuParts);
        }
    }

    /**
     * Return product weight based on weight_type attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getWeight($product = null)
    {
        if ($this->getProduct($product)->getData('weight_type')) {
            return $this->getProduct($product)->getData('weight');
        } else {
            $weight = 0;

            if ($this->getProduct($product)->hasCustomOptions()) {
                $customOption = $this->getProduct($product)->getCustomOption('bundle_selection_ids');
                if($customOption) {
                    $selectionIds = unserialize($customOption->getValue());
                    $selections = $this->getSelectionsByIds($selectionIds, $product);
                    foreach ($selections->getItems() as $selection) {
                        $qtyOption = $this->getProduct($product)
                            ->getCustomOption('selection_qty_' . $selection->getSelectionId());
                        if ($qtyOption) {
                            $weight += $selection->getWeight() * $qtyOption->getValue();
                        } else {
                            $weight += $selection->getWeight();
                        }
                    }
                }
            }
            return $weight;
        }
    }

    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        if ($this->getProduct($product)->hasCustomOptions()) {
            $customOption = $this->getProduct($product)->getCustomOption('bundle_selection_ids');
            if($customOption) {
                $selectionIds = unserialize($customOption->getValue());
                $selections = $this->getSelectionsByIds($selectionIds, $product);
                $virtualCount = 0;
                foreach ($selections->getItems() as $selection) {
                    if ($selection->isVirtual()) {
                        $virtualCount++;
                    }
                }
                if ($virtualCount == count($selections)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if product can be bought
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Product_Type
     * @throws Mage_Core_Exception
     */
    public function checkProductBuyState($product = null)
    {
        parent::checkProductBuyState($product);
        $product            = $this->getProduct($product);
        $productOptionIds   = $this->getOptionsIds($product);
        $productSelections  = $this->getSelectionsCollection($productOptionIds, $product);
        $selectionIds       = $product->getCustomOption('bundle_selection_ids');
        if(!$selectionIds) return $this;
        $selectionIds       = unserialize($selectionIds->getValue());
        $buyRequest         = $product->getCustomOption('info_buyRequest');
        $buyRequest         = new Varien_Object(unserialize($buyRequest->getValue()));
        $bundleOption       = $buyRequest->getBundleOption();

        if (empty($bundleOption)) {
            Mage::throwException($this->getSpecifyOptionMessage());
        }

        $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
        foreach ($selectionIds as $selectionId) {
            /* @var $selection Mage_Bundle_Model_Selection */
            $selection = $productSelections->getItemById($selectionId);
            if (!$selection || (!$selection->isSalable() && !$skipSaleableCheck)) {
                Mage::throwException(
                    Mage::helper('bundle')->__('Selected required options are not available.')
                );
            }
        }

        $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);
        $optionsCollection = $this->getOptionsCollection($product);
        foreach ($optionsCollection->getItems() as $option) {
            if ($option->getRequired() && empty($bundleOption[$option->getId()])) {
                Mage::throwException(
                    Mage::helper('bundle')->__('Required options are not selected.')
                );
            }
        }

        return $this;
    }

}
