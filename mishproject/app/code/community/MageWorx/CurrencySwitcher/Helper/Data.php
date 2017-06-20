<?php
/**
 * MageWorx
 * Currency Switcher Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CurrencySwitcher_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_AUTO_SWITCH_ENABLED   = 'mageworx_geoip/mageworx_currencyswitcher/enable';
    const XML_USER_AGENT_LIST       = 'mageworx_geoip/mageworx_currencyswitcher/user_agent_list';
    const XML_EXCEPTION_URLS        = 'mageworx_geoip/mageworx_currencyswitcher/exception_urls';

    /**
     * Checks if currency switcher module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_AUTO_SWITCH_ENABLED);
    }

    /**
     * Gets user agents, for which currency auto switch should be disabled
     *
     * @return array
     */
    public function getUserAgentList()
    {
        $agentList = Mage::getStoreConfig(self::XML_USER_AGENT_LIST);

        if (empty($agentList)) {
            return array();
        }

        if (!is_array($agentList)) {
            $agentList = array_filter(preg_split('/\r?\n/', $agentList));
            $agentList = array_map('trim', $agentList);
        }

        return $agentList;
    }

    /**
     * Gets urls, for which currency auto switch should be disabled
     *
     * @return array
     */
    public function getExceptionUrls()
    {
        $exceptionUrls = Mage::getStoreConfig(self::XML_EXCEPTION_URLS);
        
        if (empty($exceptionUrls)) {
            return array();
        }

        if (!is_array($exceptionUrls)) {
            $exceptionUrls = array_filter(preg_split('/\r?\n/', $exceptionUrls));
            $exceptionUrls = array_map('trim', $exceptionUrls);
        }

        return $exceptionUrls;
    }

    /**
     * Gets country-currency relations base
     *
     * @return bool|array
     */
    public function getCountryCurrency()
    {
        $path = Mage::getConfig()->getModuleDir('etc', 'MageWorx_CurrencySwitcher') . DS . 'country-currency.csv';
        if (file_exists($path)) {
            return file($path);
        } else {
            return false;
        }
    }

    /**
     * Gets currency code by country code
     *
     * @param string $countryCode
     * @return string
     */
    public function getCurrency($countryCode)
    {
        $curBase = $this->getCountryCurrency();
        if ($curBase !== false && count($curBase)) {
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            foreach ($curBase as $value) {
                $data = explode(';', $value);
                $curVal = trim($data[1]);
                if (Mage::helper('mageworx_geoip')->prepareCode($data[0]) == Mage::helper('mageworx_geoip')->prepareCode($countryCode)) {
                    if (strstr($curVal, ',')) {
                        $curCodes = explode(',', $curVal);
                        if ($curCodes) {
                            foreach ($curCodes as $code) {
                                $code = trim($code);
                                if (in_array($code, $codes)) {
                                    return $code;
                                }
                            }
                        }
                    } else {
                        if (in_array($curVal, $codes)) {
                            return $curVal;
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets country codes by currency code
     *
     * @param string $currencyCode
     * @return string
     */
    public function getCountryByCurrency($currencyCode)
    {
        $curBase = $this->getCountryCurrency();
        $countries = array();
        if ($curBase !== false && count($curBase)) {
            foreach ($curBase as $value) {
                $data = explode(';', $value);
                $curVal = trim($data[1]);
                if (strpos($curVal, $currencyCode) !== false) {
                    $countries[] = trim($data[0]);
                }
            }
        }

        return implode(',', $countries);
    }
}