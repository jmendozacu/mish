<?php
/**
 * MageWorx
 * Currency Switcher Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if(version_compare(Mage::getVersion(), '1.10.0', '>=')){
    class MageWorx_CurrencySwitcher_Model_PageCache_Processor_Abstract extends Enterprise_PageCache_Model_Processor {}
} else {
    class MageWorx_CurrencySwitcher_Model_PageCache_Processor_Abstract {}
}