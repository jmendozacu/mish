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


class AW_Randomprice_Model_Randomprice extends Mage_Rule_Model_Rule {
    //Randomprice's statuses
    const STATUS_PENDING = '0';
    const STATUS_STARTED = '1';
    const STATUS_ENDED = '2';



    const PRODUCT_OPTION_NAME='aw_randomprice_new_price';

    public function _construct() {
        $this->_init('awrandomprice/randomprice');
    }

    public function validate(Varien_Object $product) {

        /* disallow  GROUPED & BUNDLE products */

        $isComposite = in_array($product->getTypeId(), array(
            Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
            Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
                )
        );

        if ($isComposite) {
            return false;
        }


        if (!$this->getIsEnabled()) {
            return false;
        }

        $now = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);


        $data = $this->getData();


        //Check date
        if (
                (strtotime($data['date_from']) > strtotime($now)) ||
                (strtotime($data['date_to']) < strtotime($now))
        ) {
            return false;
        }


        if (!
                ($this->validateCustomerGroup()
                && $this->validateStore())
        ) {
            return false;
        }

        /*  allow special price option  */
        if (
                $product->getSpecialPrice()
                && !$this->getAllowSpecialPrice()
        ) {
            return false;
        }


        return parent::validate($product);
    }

    public function validateStore() {
        $storeId = Mage::app()->getStore()->getId();

        $ruleStores = explode(',', $this->getStoreIds());

        if (
                in_array($storeId, $ruleStores)
                || in_array('0', $ruleStores)
        ) {
            return true;
        }

        return false;
    }

    public function validateCustomerGroup() {

        $customerGroupId = Mage::helper('awrandomprice')->getCustomerGroupId();

        $allowedGroupIds = $this->getData('customer_group_ids');
        if (!is_array($allowedGroupIds)) {
            $allowedGroupIds = (array) explode(',', $allowedGroupIds);
        }

        if (in_array($customerGroupId, $allowedGroupIds)) {
            return true;
        }
        return false;
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getConditionsInstance() {
        return Mage::getModel('awrandomprice/randomprice_condition_combine');
    }

    /**
     * @param Varien_Object $product
     * @return bool
     */
    public function validateProductAttributes(Varien_Object $product) {


        $customerGroupId = Mage::helper('awrandomprice')->getCustomerGroupId();
        $randomRules = Mage::getModel('awrandomprice/randomprice')->getCollection()
                ->addStoreIdsFilter(Mage::app()->getStore()->getId())
                ->addAutomDisplayFilter(AW_Randomprice_Model_Source_Automation::INSIDE_PRODUCT_PAGE)
                ->addIsEnabledFilter(AW_Randomprice_Model_Source_Status::ENABLED)
                ->addFieldToFilter('status', array("in" => array(self::STATUS_STARTED, self::STATUS_PENDING)))
                ->addFieldToFilter('customer_group_ids', array("finset" => $customerGroupId))
                ->addActualDateFilter()
                ->orderByDateTo(Zend_Db_Select::SQL_ASC);


        if ($product->getSpecialPrice()) {
            $randomRules->addFieldToFilter('allow_special_price', array("eq" => 1));
        }

        foreach ($randomRules as $rule) {

            $rule->load($rule->getId());

            if ($rule->validate($product)) {
                return $rule;
            }
        }
        return false;
    }

    /**
     * @param Varien_Object $product
     * @param $randomprice
     * @return bool
     */
    public function validateProductAttributesWidget(Varien_Object $product, $randomprice) {
        if ($randomprice->validate($product)) {
            return $randomprice;
        }
        return false;
    }

    public function getNewPrice($product) {


        $price = $product->getFinalPrice();

        $maxPrice = $price + $this->getPriceIncrease() * $price / 100;
        $minPrice = $price - $this->getPriceDecrease() * $price / 100;
        $minPrice = max(0, $minPrice);


        $newPrice = rand($minPrice, $maxPrice);
        $newPrice = ceil($newPrice);

        return $newPrice;
    }

    public function validatePrice($product, $newPrice) {

        $price = $product->getFinalPrice();

        $maxPrice = $price + $this->getPriceIncrease() * $price / 100;
        $minPrice = $price - $this->getPriceDecrease() * $price / 100;
        $minPrice = max(0, $minPrice);

        if (($newPrice >= $minPrice) && ($newPrice <= $maxPrice)) {
            return true;
        }
        return false;
    }

    protected function _beforeSave() {

        parent::_beforeSave();


        //var_dump($this->getData());
        //die();
        if (is_array($this->getCustomerGroupIds())) {

            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
        }
    }

}
