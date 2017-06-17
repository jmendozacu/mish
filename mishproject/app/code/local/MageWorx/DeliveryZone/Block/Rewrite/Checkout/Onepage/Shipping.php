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

class MageWorx_DeliveryZone_Block_Rewrite_Checkout_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    /**
     * Get Address
     * @return Mage_Sales_Quote_Address
     */
    public function getAddress()
    {
        if (!$this->isCustomerLoggedIn()) {
            $address = $this->getQuote()->getShippingAddress();
        } else {
            $address = Mage::getModel('sales/quote_address');
        }
        $location = Mage::helper('deliveryzone')->getLocation();
        $address->setCountryId($location->getCountryId());
        $address->setRegionId($location->getRegionId());
        $address->setRegion($location->getRegion());
        return $address;
    }
    
    /**
     * Get Addresses Html Select
     * @param string $type
     * @return string
     */
    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                if (Mage::helper('deliveryzone')->isLocationEqual($address->getData())) {
                    $options[] = array(
                        'value' => $address->getId(),
                        'label' => $address->format('oneline')
                    );
                }
            }

            $addressId = $this->getAddress()->getId();
            if (empty($addressId)) {
                if ($type == 'billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('deliveryzone')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }

    /**
     * Return html
     * @return string
     */
    protected function _toHtml()
    {
        $html  = parent::_toHtml();
        $html .= '<script type="text/javascript">$("shipping:region_id").disable(); $("shipping:country_id").disable(); $("shipping:same_as_billing").checked = false; shipping.setSameAsBilling(false); $("shipping:same_as_billing").disable(); $("shipping:same_as_billing").up().hide();</script>';
        return $html;
    }
}