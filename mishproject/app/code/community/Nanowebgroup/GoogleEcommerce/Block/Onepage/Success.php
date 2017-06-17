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
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout success page
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Nanowebgroup_GoogleEcommerce_Block_Onepage_Success extends Mage_Core_Block_Template
{
    /**
     * @deprecated after 1.4.0.1
     */
    private $_order;

    /**
     * Retrieve identifier of created order
     *
     * @return string
     * @deprecated after 1.4.0.1
     */
    public function getOrderId()
    {
        return $this->_getData('order_id');
    }

    /**
     * Check order print availability
     *
     * @return bool
     * @deprecated after 1.4.0.1
     */
    public function canPrint()
    {
        return $this->_getData('can_view_order');
    }

    /**
     * Get url for order detale print
     *
     * @return string
     * @deprecated after 1.4.0.1
     */
    public function getPrintUrl()
    {
        return $this->_getData('print_url');
    }

    /**
     * Get url for view order details
     *
     * @return string
     * @deprecated after 1.4.0.1
     */
    public function getViewOrderUrl()
    {
        return $this->_getData('view_order_id');
    }

    /**
     * See if the order has state, visible on frontend
     *
     * @return bool
     */
    public function isOrderVisible()
    {
        return (bool)$this->_getData('is_order_visible');
    }

    /**
     * Getter for recurring profile view page
     *
     * @param $profile
     */
    public function getProfileUrl(Varien_Object $profile)
    {
        return $this->getUrl('sales/recurring_profile/view', array('profile' => $profile->getId()));
    }

    /**
     * Initialize data and prepare it for output
     */
    protected function _beforeToHtml()
    {
        
        //var_dump("local/Mage/checkout/Block/Onepage/Success.php"); exit;
        $this->_prepareLastOrder();
        $this->_prepareLastBillingAgreement();
        $this->_prepareLastRecurringProfiles();

        #custom code to generate google ecommerce
        #to render ecommerce tracking on a success.phtml, call $this->getData('ecommerce')
        
        $googleJs = $this->generateGoogleEcommerceJS();

        $this->setData('ecommerce', $googleJs);

        return parent::_beforeToHtml();
    }

    /**
     * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
     */
    protected function _prepareLastOrder()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
                $isVisible = !in_array($order->getState(),
                    Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates());
                $this->addData(array(
                    'is_order_visible' => $isVisible,
                    'view_order_id' => $this->getUrl('sales/order/view/', array('order_id' => $orderId)),
                    'print_url' => $this->getUrl('sales/order/print', array('order_id'=> $orderId)),
                    'can_print_order' => $isVisible,
                    'can_view_order'  => Mage::getSingleton('customer/session')->isLoggedIn() && $isVisible,
                    'order_id'  => $order->getIncrementId(),
                ));
            }
        }
    }

    /**
     * Prepare billing agreement data from an identifier in the session
     */
    protected function _prepareLastBillingAgreement()
    {
        $agreementId = Mage::getSingleton('checkout/session')->getLastBillingAgreementId();
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if ($agreementId && $customerId) {
            $agreement = Mage::getModel('sales/billing_agreement')->load($agreementId);
            if ($agreement->getId() && $customerId == $agreement->getCustomerId()) {
                $this->addData(array(
                    'agreement_ref_id' => $agreement->getReferenceId(),
                    'agreement_url' => $this->getUrl('sales/billing_agreement/view',
                        array('agreement' => $agreementId)
                    ),
                ));
            }
        }
    }

    /**
     * Prepare recurring payment profiles from the session
     */
    protected function _prepareLastRecurringProfiles()
    {
        $profileIds = Mage::getSingleton('checkout/session')->getLastRecurringProfileIds();
        if ($profileIds && is_array($profileIds)) {
            $collection = Mage::getModel('sales/recurring_profile')->getCollection()
                ->addFieldToFilter('profile_id', array('in' => $profileIds))
            ;
            $profiles = array();
            foreach ($collection as $profile) {
                $profiles[] = $profile;
            }
            if ($profiles) {
                $this->setRecurringProfiles($profiles);
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $this->setCanViewProfiles(true);
                }
            }
        }
    }

    private function generateGoogleEcommerceJS(){

        //Implement Affiliates Code here
       //  $ref_site = Mage::getSingleton('core/session')->getData('affiliateplus_referral_site');
        $ref_site = '';

        $_customerId =  Mage::getSingleton('customer/session')->getCustomerId();
        $lastOrderId =  Mage::getSingleton('checkout/session')->getLastOrderId();
        $order = Mage::getSingleton('sales/order')->load($lastOrderId);

        $order_subtotal = $order->getSubtotal();
        $order_grandtotal = $order->getGrandTotal();
        $collected_shipping = $order->getShippingInvoiced() > 0 ? $order->getShippingInvoiced() : 0;
         //gift card amount used
        $gift_cards_amount = $order->getBaseGiftCardsAmount() ? $order->getBaseGiftCardsAmount() : 0;
        //store credit used
        $customer_balance_amount = $order->getBaseCustomerBalanceAmount() ? $order->getBaseCustomerBalanceAmount() : 0;

        $tax_amount = $order->getBaseTaxAmount() ? $order->getBaseTaxAmount() : 0;

        $discount_amt = $order_subtotal + $collected_shipping + $tax_amount - $order_grandtotal - $gift_cards_amount - $customer_balance_amount;

        $coupon = strlen($order->getCouponCode())==0 && $discount_amt ? 'VALUED_CUST_DISC' : $order->getCouponCode();
        $affiliate = strlen($ref_site)? $ref_site : '';
        
        $html = '<!-- Google Ecommerce Analytics tag-->
                <script type="text/javascript">//<![CDATA[

                ga(\'require\', \'ecommerce\', \'ecommerce.js\');

                ga(\'ecommerce:addTransaction\', {
                     \'id\':\''.$order->getIncrementId().'\',
                     \'affiliation\': \''.$affiliate.'\',
                     \'revenue\': \''.number_format((float)$order_grandtotal, 2, '.', '').'\',
                     \'shipping\': \''.number_format((float)$collected_shipping, 2, '.', '').'\',
                     \'tax\': \''.number_format((float)$tax_amount, 2, '.', '').'\',
                     \'currency\': \''.Mage::app()->getStore()->getCurrentCurrencyCode().'\'
                });
        ';
       

       foreach($order->getAllItems() as $item){
                
          $categoryids = $item->getProduct()->getCategoryIds();
          $level = 2;

          $root_cat_name = '';
          $subroot_cat_name = '';

          foreach ($categoryids as $catid) {
             $_cat = Mage::getModel('catalog/category')->load($catid);

             //var_dump("foreach cat id:".$_cat->getId()." cat level:".$_cat->getLevel()." cat name:".$_cat->getName());

             if($_cat->getLevel()>=$level){

                if($_cat->getLevel()==2){
                    $root_cat_name = $_cat->getName();
                }elseif($_cat->getLevel()==3){
                    $subroot_cat_name = $_cat->getName();
                }
             }

          }
          //var_dump($root_cat_name.'/'.$subroot_cat_name); exit;
          $category_name = '';
          if($root_cat_name)
             $category_name = $root_cat_name;
          if($subroot_cat_name)
             $category_name .= '/'.$subroot_cat_name;
          
          

          $html .=' ga(\'ecommerce:addItem\', {
                      \'id\': \''.$order->getIncrementId().'\',
                      \'name\': \''.$item->getName().'\',
                      \'sku\': \''.$item->getSku().'\',
                      \'category\': \''.$category_name.'\',
                      \'price\': \''.number_format((float)$item->getPrice(), 2, '.', '').'\',
                      \'quantity\': \''.floor($item->getQtyOrdered()).'\',
                      \'currency\': \''.Mage::app()->getStore()->getCurrentCurrencyCode().'\'
                   });
           ';
        }

         $html .= 'ga(\'ecommerce:send\');
                //]]></script>
                <!-- Google Ecommerce Analytics tag-->';

        return $html;
   }
}
