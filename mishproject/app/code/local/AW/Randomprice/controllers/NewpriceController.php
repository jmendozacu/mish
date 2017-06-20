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


class AW_Randomprice_NewpriceController extends Mage_Core_Controller_Front_Action {

    public function forAction() {

        $productId = (int) $this->getRequest()->getParam('product');
        $ruleId = (int) $this->getRequest()->getParam('rule');

        $response = '<script type="text/javascript">window.location.reload();</script>';

        $rule = Mage::getModel('awrandomprice/randomprice')->load($ruleId);

        if ($productId && $rule->getId()) {

            $block = $this->getLayout()->createBlock('catalog/product_view')
                    ->setProductId($productId);

            $product = $block->getProduct();

            if ($rule->validate($product) && !$this->_pleaseWait($rule, $product)) {

                $newPrice = $rule->getNewPrice($product);

                if ($newPrice) {

                    Mage::helper('awrandomprice')->setRandomPrice($productId, $ruleId, $newPrice);
                    $product->setHasOptions(true);
                    $product->setFinalPrice($newPrice);
                }
                if (
                        !(method_exists(Mage::helper('catalog'), 'canApplyMsrp'))
                        || !(Mage::helper('catalog')->canApplyMsrp($product))
                ) {
                    $response = "
                    <script type=\"text/javascript\"> 
                        $('product_addtocart_form').reset();    
                        optionsPrice = new Product.OptionsPrice({$block->getJsonConfig()});
                        optionsPrice.reload();
                    </script>";
                }
            }
        }


        if ($this->isAjax()) {
            $this->getResponse()->setBody($response);
        } else {
            $this->_redirectReferer();
        }
        return;
    }

    protected function _pleaseWait($rule, $product) {

        if ($rule->getDelayRetry()) {
            $store_id = Mage::app()->getStore()->getStoreId();

            $userId = (Mage::getSingleton('customer/session')->isLoggedIn()) ?
                    Mage::getSingleton('customer/session')->getCustomer()->getId() :
                    0;
            $var = 'aw_randomprice_wait_' . $store_id . '_' . $product->getId() . '_' . $rule->getId() . '_' . $userId;

            $now = time();


            $data = Mage::getSingleton('core/session')->getData($var);
            if (!$data) {
                Mage::getModel('core/cookie')->get($var);
            }

            if ($data) {

                $timeLeft = time() - $data;
                $timeLeft = $rule->getDelayRetry() - $timeLeft;
                $timeLeft = max(0, $timeLeft);

                if ($timeLeft) {
                    $message = Mage::helper('awrandomprice')->__("Please wait %s seconds before trying again", $timeLeft);
                    Mage::getSingleton('core/session')->addError($message);
                    return true;
                } else {
                    Mage::getSingleton('core/session')->setData($var, $now);
                }
            } else {
                Mage::getModel('core/cookie')->set($var, $now, $rule->getDelayRetry());
                Mage::getSingleton('core/session')->setData($var, $now);
            }
        }


        return false;
    }

    /* for Magento 1411 */
    public function isAjax() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return true;
        }
        if ($this->getRequest()->getParam('ajax') || $this->getRequest()->getParam('isAjax')) {
            return true;
        }
        return false;
    }

}
