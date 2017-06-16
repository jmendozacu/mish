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

class MageWorx_DeliveryZone_Model_Rates_Action_Product extends Mage_Rule_Model_Action_Abstract
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'rate_price'=>Mage::helper('deliveryzone')->__('Rate price'),
        ));
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            'overwrite'         => Mage::helper('deliveryzone')->__('Overwrite Shipping Cost'),
            'surcharge'         => Mage::helper('deliveryzone')->__('Add Surcharge'),
            'disable'           => Mage::helper('deliveryzone')->__('Hide Shipping Method'),
            'enable'            => Mage::helper('deliveryzone')->__('Display Shipping Method'),
            'enableandoverwrite'=> Mage::helper('deliveryzone')->__('Display Shipping Method & Overwrite Shipping Cost'),
        ));
        return $this;
    }
    
    public function getOperatorOptions() {
        $this->loadOperatorOptions();
        return $this->getOperatorOption();
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().Mage::helper('deliveryzone')->__("Update product's %s %s: %s", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml());
        $html.= $this->getRemoveLinkHtml();
        return $html;
    }
}
