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

class MageWorx_DeliveryZone_Model_Observer
{
    /**
     * Filter categories by zone
     * @param object $observer
     */
    public function filterCategoriesByZone($observer)
    {
        // ONLY IS HIDE MODE
        if(Mage::getStoreConfig('mageworx_deliveryzone/deliveryzone/zone_mode') !== 'hide') return ;
        
        $location = Mage::helper('deliveryzone')->getLocation();
        $zone = Mage::getModel('deliveryzone/zone');
        $collection = $observer->getEvent()->getCategoryCollection();
        if ($categoryIds = $zone->getCategoryIdsByLocation($location->getZoneIds())) {
            $collection->addFieldToFilter('entity_id', array('in' => $categoryIds));
        }
    }

    /**
     * Filter products by zone
     * @param object $observer
     */
    public function filterProductsByZone($observer)
    {
        // ONLY IS HIDE MODE
        if(Mage::getStoreConfig('mageworx_deliveryzone/deliveryzone/zone_mode') !== 'hide') return ;
        
        $location = Mage::helper('deliveryzone')->getLocation();
        if (Mage::registry('disable_products_filter', 0)) return;
        $zone = Mage::getModel('deliveryzone/zone');
        if ($productIds = $zone->getProductIdsByLocation($location->getZoneIds())) {
            $collection = $observer->getEvent()->getCollection();
            $collection->addIdFilter($productIds);
        }
    }

    /**
     * Filter categories
     * @param object $observer
     */
    public function isCategoryAllowed($observer)
    {
        // ONLY IS HIDE MODE
        if(Mage::getStoreConfig('mageworx_deliveryzone/deliveryzone/zone_mode') !== 'hide') return ;
        
        $location = Mage::helper('deliveryzone')->getLocation();
        $zone = Mage::getModel('deliveryzone/zone');
        if ($categoryIds = $zone->getCategoryIdsByLocation($location->getZoneIds())) {
            $category = $observer->getEvent()->getCategory();
            if (!in_array($category->getId(), $categoryIds)) {
                $action = $observer->getEvent()->getControllerAction();
                Mage::getSingleton('core/session')->addError(Mage::helper('deliveryzone')->__('Category \'%s\' is unavailable', $category->getName()));
                $action->getResponse()->setRedirect(Mage::getUrl());
            }
        }
    }

    /**
     * Filter products
     * @param object $observer
     */
    public function isProductAllowed($observer)
    {
        // ONLY IS HIDE MODE
	if(Mage::getStoreConfig('mageworx_deliveryzone/deliveryzone/zone_mode') !== 'hide') return ;
        
        $location = Mage::helper('deliveryzone')->getLocation();
        $zone = Mage::getModel('deliveryzone/zone');
        if ($productIds = $zone->getProductIdsByLocation($location->getZoneIds())) {
            $product = $observer->getEvent()->getProduct();
            $diff = in_array($product->getId(), $productIds);
            if (!$diff) {
                Mage::throwException(Mage::helper('deliveryzone')->__('Product \'%s\' is unavailable for purchase', $product->getName()));
            }
        } else {
            return true;
        }
    }

    /**
     * Filter products
     * @param object $observer
     */
    public function isProductAvailibleCart($observer)
    {
        // ONLY NOTICE MODE
        if(Mage::getStoreConfig('mageworx_deliveryzone/deliveryzone/zone_mode') !== 'notice') return ;
        
        if($observer->getBlock() instanceof Mage_Checkout_Block_Cart_Item_Renderer) 
        {
            $zones = Mage::helper('deliveryzone')->registerZoneProduct($observer->getBlock()->getItem()->getProduct()->getId());
            if(is_array(Mage::registry('unavailible_shippingsuite_zone_product_'.$zones)) && !in_array($observer->getBlock()->getItem()->getProduct()->getId(),Mage::registry('unavailible_shippingsuite_zone_product_'.$zones))) {
              $observer->getBlock()->getItem()->setMessage(array('text'=>Mage::helper('core')->__('Please note that this product cannot be shipped to your country')));
              Mage::register('shippingsuite_checkout_disable', true, true);
            }
        }
    }

    /**
     * Reset shipping address
     * @param object $observer
     */
    public function resetShippingAddress($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if(!$quote->getCustomer()->getId()) {
            return ;
        }
        $address = $quote->getShippingAddress();
        if(!count($address->getData())) {
            return ;
        }
        if(!Mage::registry('delivery_zone_set_location')) {     // need to fix recursion
            Mage::register('delivery_zone_set_location',true);
            Mage::helper('deliveryzone')->setLocation($address->getData());
        }
        $address->addData(Mage::helper('deliveryzone')->getLocation()->getData());
        $address->setCollectShippingRates(true);
    }
    
    /**
     * Save data after customer group save
     * @param object $observer
     */
    public function customerGroupSaveAfter($observer)
    {
        $customerGroup = $observer->getObject();
        $itemShippingMethods    = Mage::app()->getRequest()->getParam('active');
        if($itemShippingMethods) {
            $itemShippingSubMethods = Mage::app()->getRequest()->getParam('methods');
            // save zone shipping methods
            $sMethodModel = Mage::getModel('deliveryzone/customer_group');
                // remove all items by zone id
                foreach ($sMethodModel->getCollection()->loadByGroupId($customerGroup->getId()) as $item) {
                    $item->delete();
                }

            foreach ($itemShippingMethods as $method=>$isActive) {
                if(!$isActive) continue;

                $subMethodIds = '';
                if(isset($itemShippingSubMethods[$method])) {
                    $subMethodIds = join(',',$itemShippingSubMethods[$method]);
                }
                $sMethodModel->setData(array('customer_group_id'=>$customerGroup->getId(),'carrier_id'=>$method,'allowed_methods'=>$subMethodIds))->save();
            }
        }
    }
    
    /**
     * Add freeshipping flag to product
     * @param object $observer
     */
    public function addFreeShippingFlag($observer) {
        if($observer->getBlock()->getType()=="catalog/product_price") {
            $product=$observer->getBlock()->getProduct();
            if(Mage::registry('deliveryzone_free_shipping_'.$product->getId())) return true;
            $html = $observer->getTransport()->getHtml();
            $freeShippingMin = Mage::getStoreConfig('carriers/freeshipping/free_shipping_subtotal');
            if(Mage::getStoreConfig('mageworx_deliveryzone/tweeks/free_shipping_message') && ($freeShippingMin<=$product->getFinalPrice())) {
                $html = str_replace('div class', "div style='float:left;' class", $html);
                $html .= "<div class='freeshipping'>".Mage::helper('deliveryzone')->__('Free shipping')."</div><div class='clear_freeshipping'> </div>";
                if(Mage::app()->getRequest()->getModuleName()!="catalog") {
                    $html = str_replace("class='freeshipping'", "class='freeshipping checkout'", $html);
                }
            }
            Mage::register('deliveryzone_free_shipping_'.$product->getId(), TRUE);
            $observer->getTransport()->setHtml($html);
        }
        return true;
    }
    
    public function addShipToList($observer) {
//        echo $observer->getBlock()->getNameInLayout()."<br>";
    }
}