<?php
/**
 * MageWorx
 * Currency Switcher Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CurrencySwitcher_Model_Relations extends Mage_Core_Model_Abstract
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('mageworx_currencyswitcher/relations');
    }

    /**
     * Gets currency code from custom currency relations table
     *
     * @param string $countryCode
     * @return string
     */
    public function getCountryCurrency($countryCode)
    {
        $collection = $this->getCollection();
        $collection->getSelect()
            ->where('`countries` LIKE "%' . $countryCode . '%"');

        $relation = $collection->getFirstItem();
        if (!$relation) {
            return false;
        }

        return $relation->getCurrencyCode();
    }
}
