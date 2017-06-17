<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Randomprice
 * @version    1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Randomprice_Model_Observer {

    public function prepareCart($observer) {


        $product = Mage::getModel('catalog/product')
                ->load($observer->getBuyRequest()->getProduct());

        $isComposite =
                in_array($product->getTypeId(), array(
            Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
            Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
                )
        );

        if ($isComposite) {
            return;
        }

        $product = $observer->getProduct();

        $newPrice = Mage::helper('awrandomprice')->getRandomPrice($product);
        if ($newPrice) {
            $product->addCustomOption(AW_Randomprice_Model_Randomprice::PRODUCT_OPTION_NAME, $newPrice);
        }
    }

    public function getFinalPrice($event) {


        $product = $event->getProduct();

        $newPrice = $product->getCustomOption(AW_Randomprice_Model_Randomprice::PRODUCT_OPTION_NAME);

        if ($newPrice && $newPrice->getValue()) {
            $product->setFinalPrice($newPrice->getValue());
            return;
        }

        /* change price on product page */
        $currentProduct = Mage::registry('current_product');
        if (
                $currentProduct
                && ($currentProduct->getId() == $product->getId() )
        ) {
            $newPrice = Mage::helper('awrandomprice')->getRandomPrice($product);

            if ($newPrice) {
                $product->addCustomOption(AW_Randomprice_Model_Randomprice::PRODUCT_OPTION_NAME, $newPrice);
                $product->setFinalPrice($newPrice);
                return;
            }
        }

        return;
    }

}
