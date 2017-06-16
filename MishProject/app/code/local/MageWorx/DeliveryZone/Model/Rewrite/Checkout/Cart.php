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
 * !!!! DEPRICATED !!!!
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_DeliveryZone_Model_Rewrite_Checkout_Cart extends Mage_Checkout_Model_Cart
{
    /**
     * Contruct object
     * Filter items in checkout cart
     * @return MageWorx_DeliveryZone_Model_Rewrite_Checkout_Cart
     */
    protected function _construct()
    {
        $location = Mage::helper('deliveryzone')->getLocation();
        $zone = Mage::getModel('deliveryzone/zone');
        if (($productIds = $zone->getProductIdsByLocation($location->getZoneIds())) && $this->getSummaryQty() > 0) {
            Mage::register('disable_products_filter', 1);
            foreach ($this->getQuote()->getAllItems() as $item) {
                $productId = (int) $item->getProductId();
                if ($productId && !in_array($productId, $productIds)) {
                    $product = $this->_getProduct($productId);
                    $this->removeItem($item->getId())->save();

                    Mage::getSingleton('checkout/session')
                    	->addNotice(Mage::helper('deliveryzone')->__('%s was removed from your shopping cart', $product->getName()))
                        ->setCartWasUpdated(true);
                }
            }
            Mage::unregister('disable_products_filter');
        }
        return $this;
    }

    /**
     * Add product
     * @param object $product
     * @param string $info
     * @return Mage_Checkout_Model_Cart
     */
    public function addProduct($product, $info = null)
    {
        $product = $this->_getProduct($product);
        Mage::dispatchEvent('checkout_cart_add_product_before', array('product' => $product));
        return parent::addProduct($product, $info);
    }
    
    /**
     * 
     * @param string $productIds
     * @return Mage_Checkout_Model_Cart
     */
    public function addProductsByIds($productIds)
    {
        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $productId = (int) $productId;
                if (!$productId) {
                    continue;
                }
                $product = $this->_getProduct($product);
                Mage::dispatchEvent('checkout_cart_add_product_before', array('product' => $product));
            }
        }
        return parent::addProductsByIds($productIds);
    }
    
    /**
     * Get Product by product info
     * @param Mage_Catalog_Model_Product | int $productInfo
     * @return Varien_Object | Mage_Catalog_Model_Product
     */
    protected function _getProduct($productInfo)
    {
        if ($productInfo instanceof Mage_Catalog_Model_Product) {
            $product = $productInfo;
        }
        elseif (is_int($productInfo)) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productInfo);
        }
        else {
            return new Varien_Object();
        }
        return $product;
    }
}