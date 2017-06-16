<?php
/************************************************************************
 * 
 * jtechextensions @ J-Tech LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.jtechextensions.com/LICENSE-M1.txt
 *
 * @package    Tiered Pricing By Percent
 * @copyright  Copyright (c) 2012-2013 jtechextensions @ J-Tech LLC. (http://www.jtechextensions.com)
 * @license    http://www.jtechextensions.com/LICENSE-M1.txt
************************************************************************/
class Jtech_TierPrices_Block_Adminhtml_Catalog_Product_Edit_Action_Attribute_Tab_Tierprices
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('catalog')->__('Add Tier'),
                'onclick' => 'return tierPriceControl.addItem()',
                'class' => 'add'
            ));
        $button->setName('add_tier_price_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
    
    public function getAttribute()
    {
        return Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'tier_price');
    }
    
    public function isScopeGlobal()
    {
        true;   
    }
    
    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }

        $this->_websites = array(
            0 => array(
                'name' => Mage::helper('catalog')->__('All Websites'),
                'currency' => Mage::app()->getBaseCurrencyCode()
            )
        );

        $websites = Mage::app()->getWebsites(false);
        $productWebsiteIds  = Mage::app()->getWebsites();
        foreach ($websites as $website) {
            /** @var $website Mage_Core_Model_Website */
            if (!in_array($website->getId(), $productWebsiteIds)) {
                continue;
            }
            $this->_websites[$website->getId()] = array(
                'name' => $website->getName(),
                'currency' => $website->getBaseCurrencyCode()
            );
        }

        return $this->_websites;
    }
    
    protected function _getInitialCustomerGroups()
    {
        return array(Mage_Customer_Model_Group::CUST_GROUP_ALL => Mage::helper('catalog')->__('ALL GROUPS'));
    }
    
    public function isAllowChangeWebsite()
    {
        if (!$this->isShowWebsiteColumn() || $this->getStoreId()) {
            return false;
        }
        return true;
    }
    
    public function getFieldSuffix()
    {
        return 'tierprices';
    }

    public function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store');
        return intval($storeId);
    }

    public function getTabLabel()
    {
        return Mage::helper('catalog')->__('Tier Prices');
    }

    public function getTabTitle()
    {
        return Mage::helper('catalog')->__('Tier Prices');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
