<?php
/**
 * MageWorx
 * Currency Switcher Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CurrencySwitcher_Model_Observer
{
    /**
     * Automatically switches currency
     *
     * @param   Varien_Event_Observer $observer
     * @return  MageWorx_CurrencySwitcher_Model_Observer
     */
    public function switchCurrency(Varien_Event_Observer $observer)
    {
        $switcher = Mage::getSingleton('mageworx_currencyswitcher/switcher');
        if (!$switcher->isAllowed()) {
            return $this;
        }

        $geoipHelper = Mage::helper('mageworx_geoip');
        $currencyCookie = $geoipHelper->getCookie('currency_code');
        $mageStore = Mage::app()->getStore();

        $geoipCountry = Mage::app()->getRequest()->getParam('geoip_country');
        if ($mageStore->getCurrentCurrencyCode() != $currencyCookie || $geoipCountry) {
            $currency = null;

            if ($geoipCountry && $geoipHelper->checkCountryCode($geoipCountry)) {
                $currency   = $switcher->getCurrency($geoipCountry);
            } elseif ($currencyCookie) {
                $currency = $currencyCookie;
            } else {
                $geoip      = Mage::getModel('mageworx_geoip/geoip')->getCurrentLocation();
                $currency   = $switcher->getCurrency($geoip->getCode());
            }
            if ($currency && ($mageStore->getCurrentCurrencyCode() != $currency)) {
                $mageStore->setCurrentCurrencyCode($currency);

                if (Mage::getSingleton('checkout/session')->hasQuote()) {
                    Mage::getSingleton('checkout/session')->getQuote()
                        ->collectTotals()
                        ->save();
                }
            } else {
                $geoipHelper->setCookie('currency_code', $mageStore->getCurrentCurrencyCode());
            }
        }

        return $this;
    }

    /**
     * Changes module's cookie "currency_code" when currency is changed manually
     *
     * @param   Varien_Event_Observer $observer
     * @return  MageWorx_CurrencySwitcher_Model_Observer
     */
    public function setCurrency(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('mageworx_currencyswitcher')->isEnabled() || Mage::app()->getStore()->isAdmin()) {
            return false;
        }

        $filter = new Zend_Filter_StripTags();
        $currency = $filter->filter(Mage::app()->getFrontController()->getRequest()->getParam('currency'));
        Mage::helper('mageworx_geoip')->setCookie('currency_code', $currency);

        return $this;
    }

    /**
     * Adds currency-switcher configuration and "country => currency" relations to cache
     *
     * @param $observer
     * @return MageWorx_CurrencySwitcher_Model_Observer
     */
    public function refreshCurrencySwitcherCache($observer)
    {
        if (!Mage::helper('core')->isModuleEnabled('Enterprise_PageCache')) {
            return $this;
        }

        if ($observer->getType() && $observer->getType() != 'full_page') {
            return $this;
        }

        $cacheData = array();
        $relations = Mage::getModel('mageworx_currencyswitcher/relations')->getCollection();
        foreach ($relations as $item) {
            $cacheData['relations'][$item->getCurrencyCode()] = explode(',', $item->getCountries());
        }

        $cacheData['config'] = array(
            'is_city_db_type' => Mage::getStoreConfig(MageWorx_GeoIP_Helper_Data::XML_GEOIP_DATABASE_TYPE) == 2,
            'db_path' => Mage::helper('mageworx_geoip')->getDatabasePath(),
        );

        Mage::app()->getCache()->save(serialize($cacheData), 'mageworx_currencyswitcher_config', array(), 86400 * 365);

        return $this;
    }
}
