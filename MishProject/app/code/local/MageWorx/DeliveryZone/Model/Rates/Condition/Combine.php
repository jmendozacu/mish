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

class MageWorx_DeliveryZone_Model_Rates_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('deliveryzone/rates_condition_combine');
    }

     /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $itemAttributes     = array();
        $cartAttributes     = array();
        
        $productCondition = Mage::getModel('deliveryzone/rates_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        
        $addressCondition = Mage::getModel('salesrule/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        unset($addressAttributes['shipping_method']);
        // Add custom attributes
        $addressAttributes['firstname'] = Mage::helper('deliveryzone')->__('First Name');
        $addressAttributes['lastname'] = Mage::helper('deliveryzone')->__('Last Name');
        
        foreach ($addressAttributes as $code=>$label) {
            $cartAttributes[] = array('value'=>'salesrule/rule_condition_address|'.$code, 'label'=>$label);
        }
        foreach ($productAttributes as $code=>$label) {
            $itemAttributes[] = array('value'=>'deliveryzone/rates_condition_product|'.$code, 'label'=>$label);
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
//            array('value'=>'deliveryzone/rates_condition_combine', 'label'=>Mage::helper('deliveryzone')->__('Conditions Combination')),
            array('label'=>Mage::helper('deliveryzone')->__('Cart Attribute'), 'value'=>$cartAttributes),
            array('label'=>Mage::helper('deliveryzone')->__('Product Attribute'), 'value'=>$itemAttributes),
        ));
        return $conditions;
    }

    /**
     * Collect Validated Attributes
     * @param object $productCollection
     * @return MageWorx_DeliveryZone_Model_Rates_Condition_Combine
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
    
    /**
     * Validate rule
     * @param Varien_Object $object
     * @return boolean
     */
     public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return true;
        }
        
        if($object->getAddressType() == 'billing') {
            $checkout = Mage::getSingleton('checkout/session');
            $quote    = $checkout->getQuote();
            $object = $quote->getShippingAddress();
        //    echo "<pre>"; print_r($object->getData()); exit;
        }

        $all    = $this->getAggregator() === 'all';
        $validated = null;
        $true   = (bool)$this->getValue();

        foreach ($this->getConditions() as $cond) {
            if($cond instanceof MageWorx_DeliveryZone_Model_Rates_Condition_Product){
                if (Mage::app()->getStore()->isAdmin()) {
                    $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
                } else {
                    $quote = Mage::getSingleton('checkout/cart')->getQuote();           
                }
               // echo '<pre>'; print_r($quote->getId()); exit;
                foreach ($quote->getAllItems() as $item) {
                    $found = $all;
                    foreach ($this->getConditions() as $cond) {
                        $validated = $cond->validate($item);
                        if (($all && !$validated) || (!$all && $validated)) {
                            $found = $validated;
                            break;
                        }
                    }
                    if (($found && $true) || (!$true && $found)) {
                        break;
                    }
                }
            }
            else {
                
                $validated = $cond->validate($object);
            }
            
            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }
        return $all ? true : false;
    }
}
