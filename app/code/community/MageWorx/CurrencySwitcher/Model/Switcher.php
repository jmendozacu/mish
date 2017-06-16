<?php
/**
 * MageWorx
 * Currency Switcher Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CurrencySwitcher_Model_Switcher extends Mage_Core_Model_Abstract
{
    /**
     * Checks if currency auto switch is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        if (!Mage::helper('mageworx_currencyswitcher')->isEnabled() || Mage::app()->getStore()->isAdmin()) {
            return false;
        }

        $exceptionUrls = Mage::helper('mageworx_currencyswitcher')->getExceptionUrls();
        if ($this->checkExceptionUrls($exceptionUrls) === false) {
            return false;
        }

        $userAgentList = Mage::helper('mageworx_currencyswitcher')->getUserAgentList();
        if ($this->checkUserAgentList($userAgentList) === false) {
            return false;
        }

        return true;
    }

    /**
     * Checks if currency switcher is available for URLs
     *
     * @param array $exceptionUrls
     * @return bool
     */
    protected function checkExceptionUrls(array $exceptionUrls)
    {
        $requestString = Mage::app()->getRequest()->getRequestString();
        foreach ($exceptionUrls as $url) {
            $url = str_replace('*', '.*?', $url);
            if (preg_match('!^' . $url . '$!i', $requestString)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if currency switcher is available for User Agents
     *
     * @param array $userAgentList
     * @return bool
     */
    protected function checkUserAgentList(array $userAgentList)
    {
        $userAgent = Mage::helper('mageworx_geoip/http')->getHttpUserAgent();

        if (!$userAgent) {
            return true;
        }
        
        foreach ($userAgentList as $agent) {
            $agent = str_replace('*', '.*', $agent);
            $agent = str_replace('/', '\/', $agent);
            if (preg_match("/{$agent}$/i", $userAgent)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns currency code for auto-switch
     *
     * @param string $countryCode
     * @return string
     */
    public function getCurrency($countryCode)
    {
        $currency = Mage::helper('mageworx_currencyswitcher')->getCurrency($countryCode);

        $customCurrency = Mage::getModel('mageworx_currencyswitcher/relations')->getCountryCurrency($countryCode);
        if ($customCurrency) {
            $currency = $customCurrency;
        }

        return $currency;
    }
}