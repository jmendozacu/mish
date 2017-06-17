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


class AW_Randomprice_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isEditAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('awrandomprice/randomprice/new');
    }

    public function isViewAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('awrandomprice/randomprice/list');
    }

    public function getExtDisabled() {
        return Mage::getStoreConfig('advanced/modules_disable_output/AW_Randomprice');
    }

    public function getCustomerGroupId() {
        return Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getCustomerGroupId() : 0;
    }

    public function getRandomPrice($product) {


        if (!$product || !$product->getId()) {
            return false;
        }
        $store_id = Mage::app()->getStore()->getStoreId();

        $var = 'aw_randomprice_' . $store_id . '_' . $product->getId();

        $data = Mage::getSingleton('core/session')->getData($var);

        $ruleId = isset($data['rule_id']) ? $data['rule_id'] : false;
        $newPrice = isset($data['new_price']) ? $data['new_price'] : false;


        if (!$ruleId || !$newPrice) {
            return false;
        }

        $randompriceRule = Mage::getModel('awrandomprice/randomprice')->load($ruleId);

        if (
                !$randompriceRule->validate($product)
                || !$randompriceRule->validatePrice($product, $newPrice)
        ) {
            return false;
        }


        return $newPrice;
    }

    public function setRandomPrice($productId, $ruleId, $newPrice) {

        $store_id = Mage::app()->getStore()->getStoreId();

        $var = 'aw_randomprice_' . $store_id . '_' . $productId;

        $data = array(
            'rule_id' => $ruleId,
            'new_price' => $newPrice,
        );
        Mage::getSingleton('core/session')->setData($var, $data);
    }

    public function recursiveReplace($search, $replace, $subject) {
        if (!is_array($subject))
            return $subject;

        foreach ($subject as $key => $value)
            if (is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif (is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }

}
