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

class MageWorx_DeliveryZone_IndexController extends Mage_Core_Controller_Front_Action
{
    private $_origQuoteId;
    private $_fakeQuoteId;
    /**
     * Index action
     */
    public function indexAction()
    {
        $data = $this->getRequest()->getPost('shippingzone', array());
        if (isset($data['country_id'])) {
            $country = Mage::getModel('directory/country')->load($data['country_id']);
            if ($country) {
                if (isset($data['region_id'])) {
                    $region = Mage::getModel('directory/region')->load($data['region_id']);
                    if ($region->getCountryId() != $data['country_id']) {
                        unset($data['region_id']);
                    }
                }
                Mage::helper('deliveryzone')->setLocation($data);
            }
        }
        // Compatibility with Varnish cache. v.1.0
        if ((string)Mage::getConfig()->getModuleConfig('Phoenix_VarnishCache')->active == 'true'){
            Mage::app()->cleanCache();
        }
        $this->_redirectReferer();
    }

    /**
     * Create redirect
     * @param string $defaultUrl
     * @return MageWorx_DeliveryZone_IndexController
     */
    protected function _redirectReferer($defaultUrl = null)
    {
        $refererUrl = $this->_getRefererUrl();
        if (empty($refererUrl)) {
            $refererUrl = empty($defaultUrl) ? Mage::app()->getStore()->getBaseUrl() : $defaultUrl;
        }

        $this->getResponse()->setRedirect($refererUrl);
        return $this;
    }

    /**
     * Get cehckout session
     * @return object
     */ 
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get Quote function
     * @return object
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    
    private function _createQuote() {
        $customer = Mage::getSingleton('checkout/session')->getQuote()->getCustomer();
        $q = Mage::getSingleton('checkout/session')->getQuote();
        $quote    = Mage::getModel('sales/quote')->assignCustomer($customer);
        $quote->setStore(Mage::app()->getStore());
        $quote->setIsCheckoutCart(true);
        $quote->save();
       // echo '<pre>'; print_r($quote->getData());
        $this->_fakeQuoteId = $quote->getId();
    }
    
    private function _removeQuote($quoteId) {
       try {
            $quote = Mage::getModel("sales/quote")->load($quoteId);
            $quote->setIsActive(false);
            $quote->delete();
       } catch(Exception $e) {
            return $e->getMessage();
       }
       return true;
    }

        /** 
     * Shipping estimate by address and product
     * 
     */
    public function estimatePostAction() {
        // Compatibility with Varnish cache. v.1.0
        if ((string)Mage::getConfig()->getModuleConfig('Phoenix_VarnishCache')->active == 'true'){
            Mage::app()->cleanCache();
        }
        $data = $this->getRequest()->getPost();
        if(!isset($data['qty']) || !$data['qty']) {
            $data['qty'] = 1;
        }
        $key = "";
        $this->_createQuote(); 
        $this->_origQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        $this->getCheckout()->replaceQuote(Mage::getModel('sales/quote')->load($this->_fakeQuoteId));
        $quote    = $this->getQuote();
        $_data = array("rates"=>array(),"address"=>array());
        $quote->getShippingAddress()
                    ->setRegionId(isset($data['region_id'])?$data['region_id']:"")
                    ->setRegion((isset($data['region']) && isset($data['region_id']) && !$data['region_id'])?$data['region']:"")
                    ->setCity(isset($data['estimate_city'])?$data['estimate_city']:"")
                    ->setPostCode(isset($data['estimate_postcode'])?$data['estimate_postcode']:"")
                    ->setPostcode(isset($data['estimate_postcode'])?$data['estimate_postcode']:"")
                    ->setCountryId(isset($data['country_id'])?$data['country_id']:"");
        $request = new Varien_Object($data);
        if(isset($data['product_id'])) {
            $_product = Mage::getModel('catalog/product')->load($data['product_id']);
            $_product->getStockItem()->setUseConfigManageStock(false);
            $_product->getStockItem()->setManageStock(false);
            $_product->getStockItem()->setIsQtyDecimal(true);
            $_product->getStockItem()->setIsDecimalDivided(true);
            $_product->getStockItem()->setUseConfigEnableQtyIncrements(false);
            $_product->getStockItem()->setData('enable_qty_increments',true);
            $item = $quote->addProduct($_product,$request);
            $item->setProduct($_product);
            $item->save();
            Mage::register("deliveryzone_add_product_to_quote", TRUE, TRUE);
            Mage::register("deliveryzone_quote_item", $item->getId(), TRUE);
            $key = "delivery_zone_product".$_product->getId();
        }
       
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->setData('is_fake',true);
        $quote->collectTotals(true)->save();
        $quote->getShippingAddress()->collectTotals()->save();
        $quote->getShippingAddress()->collectShippingRates();
       
        $rates = $quote->getShippingAddress()->getShippingRatesCollection(); 
        foreach ($rates as $rate) {
            if(!isset($_data['rates'][$rate->getCarrier()])) $_data['rates'][$rate->getCarrier()] = array();
            if(!isset($_data['rates'][$rate->getCarrier()][$rate->getCode()])) {
                if(!$rate->getErrorMessage()) {
                    $_data['rates'][$rate->getCarrier()][$rate->getCode()] = $rate->getData();
                }
            }
        }
        $_data['rates'] = array_filter($_data['rates']);
        $_data["address"] = $data;
        $session = Mage::getSingleton('customer/session');
        $session->setData($key,$_data);

        $this->getCheckout()->replaceQuote(Mage::getModel('sales/quote')->load($this->_origQuoteId));
        $this->_removeQuote($this->_fakeQuoteId);

        $this->_redirectReferer();
    }
}