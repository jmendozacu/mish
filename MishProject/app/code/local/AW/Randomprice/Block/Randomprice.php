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


class AW_Randomprice_Block_Randomprice extends Mage_Core_Block_Template {

    protected $_appliedRule = null;

    public function getProduct() {
        $this->setData('product', Mage::registry('current_product'));
        if (!$this->getData('product')) {

            $product = Mage::getModel('catalog/product')->load($this->getProductId());

            if (!$product->getId()) {
                return new Varien_Object();
            }

            $this->setData('product', $product);
        }

        return $this->getData('product');
    }

    protected function _beforeToHtml() {

        if ($this->getRandompriceid() != null) {
            $tmp = Mage::getModel('awrandomprice/randomprice')->load($this->getRandompriceid());
            if ($tmp->getAutomDisplay() == AW_Randomprice_Model_Source_Automation::NO) {
                if (Mage::registry('current_product') != null) {
                    $tmp = $tmp->validateProductAttributesWidget(Mage::registry('current_product'), $tmp);
                }
                $this->_appliedRule = $tmp;
            }
        } else {
            if (Mage::registry('current_product') != null) {
                $this->_appliedRule = $this->validateProduct();
            }
        }

        if ($this->_appliedRule && $this->_appliedRule->getId()) {
            $rule = $this->_appliedRule;
            $product = $this->getProduct();
            if ($rule->validate($product)) {
                $this->setTemplate('aw_randomprice/blocks.phtml');
            }
        }
    }

    public function validateProduct() {

        $product = $this->getProduct();

        if (!$product->getId()) {
            return $this;
        }

        $validationModel = Mage::getModel('awrandomprice/randomprice');

        if ($applied = $validationModel->validateProductAttributes($product)) {
            return $applied;
        }
        return $this;
    }

    public function getBlockHtml($rule) {

        $product = $this->getProduct();

        $url = Mage::getUrl('awrandomprice/newprice/for', array(
                    'product' => $product->getId(),
                    'rule' => $rule->getId(),
                        )
        );

        $template = $rule->getTemplate();
        $title = $rule->getBlockTitle();

        $vars = array('{{randomprice_link}}', '{{link_title}}');
        $values = array($url, $title);


        $content = str_replace($vars, $values, $template);

        return $content;
    }

}
