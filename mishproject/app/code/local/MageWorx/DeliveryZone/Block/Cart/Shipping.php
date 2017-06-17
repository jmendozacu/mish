<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class MageWorx_DeliveryZone_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
    private $_location;
    /**
     * Get Estimate Country Id
     *
     * @return string
     */
    public function getEstimateCountryId()
    {
        $countryId = $this->getAddress()->getCountryId();
        if(!$countryId) {
             $countryId = $this->getCurrentLocation()->getCountryId();
        }
        return $countryId;
    }

    /**
     * Get Estimate Region Id
     *
     * @return mixed
     */
    public function getEstimateRegionId()
    {
         $regionId = $this->getAddress()->getRegionId();
         if(!$regionId) {
             $regionId = $this->getCurrentLocation()->getRegionId();
         }
         return $regionId;
    }

    /**
     * Get Estimate Region
     *
     * @return string
     */
    public function getEstimateRegion()
    {   
        $region = $this->getAddress()->getRegion();
        if(!$region) {
            $region = $this->getCurrentLocation()->getRegion();
        }
        return $region;
    }
    
    private function getCurrentLocation() {
        if(!$this->_location) {
            $this->_location = Mage::helper('deliveryzone')->getLocation();
        }
        return $this->_location;
    }

}
