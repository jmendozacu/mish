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

require_once 'Mage/Checkout/controllers/OnepageController.php';

class MageWorx_DeliveryZone_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    public function getFullActionName($delimiter = '_')
    {
        return $this->getRequest()->getControllerName() . $delimiter . $this->getRequest()->getActionName();
    }

    public function saveBillingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $isLocationEqual = Mage::helper('deliveryzone')->isLocationEqual($data);
//            if (!$isLocationEqual) {
//                $data['use_for_shipping'] = 0;
//            }
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1 /*&& $isLocationEqual*/) {
                	$result['goto_section']   = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    public function saveShippingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $location = Mage::helper('deliveryzone')->getLocation();

            foreach ($location->getData() as $key => $value) {
                if (!isset($data[$key]) || empty($data[$key])) {
                    $data[$key] = $value;
                }
            }
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section']   = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
}