<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Randomprice
 * @version    1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Randomprice_Block_Adminhtml_Randomprice_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $_fieldset = $form->addFieldset('randomprice_form', array('legend' => Mage::helper('awrandomprice')->__('General')));
        $_data = Mage::registry('randomprice_data');


        $_fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => $this->__('Name'),
            'title' => $this->__('Name'),
            'required' => true
        ));

        if ($_data != null) {
            if ($_data->getData('status') === null)
                $_data->setData('status', 1);
        }

        $_fieldset->addField('is_enabled', 'select', array(
            'name' => 'is_enabled',
            'label' => $this->__('Status'),
            'title' => $this->__('Status'),
            'values' => Mage::getModel('awrandomprice/source_status')->toOptionArray()
        ));

        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $_fieldset->addField('date_from', 'date', array(
            'name' => 'date_from',
            'label' => Mage::helper('awrandomprice')->__('From:'),
            'title' => Mage::helper('awrandomprice')->__('From:'),
            'format' => $outputFormat,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'time' => true
        ));

        $_fieldset->addField('date_to', 'date', array(
            'name' => 'date_to',
            'label' => Mage::helper('awrandomprice')->__('To:'),
            'title' => Mage::helper('awrandomprice')->__('To:'),
            'class' => 'required-entry',
            'required' => true,
            'format' => $outputFormat,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'time' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $_fieldset->addField('store_ids', 'multiselect', array(
                'name' => 'store_ids',
                'label' => $this->__('Store View'),
                'required' => TRUE,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(FALSE, TRUE),
            ));
        } else {
            if ($_data->getStoreIds() && is_array($_data->getStoreIds())) {
                $_stores = $_data->getStoreIds();
                if (isset($_stores[0]) && $_stores[0] != '')
                    $_stores = $_stores[0];
                else
                    $_stores = 0;
                $_data->getStoreIds($_stores);
            }

            $_fieldset->addField('store_ids', 'hidden', array(
                'name' => 'store_ids'
            ));
        }

        if (!$_data->getStoreIds())
            $_data->getStoreIds(0);



        $customerGroups = Mage::getResourceModel('customer/group_collection')
                        ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value'] == 0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array('value' => 0, 'label' => Mage::helper('catalogrule')->__('NOT LOGGED IN')));
        }

        $_fieldset->addField('customer_group_ids', 'multiselect', array(
            'name' => 'customer_group_ids[]',
            'label' => Mage::helper('catalogrule')->__('Customer Groups'),
            'title' => Mage::helper('catalogrule')->__('Customer Groups'),
            'required' => true,
            'values' => $customerGroups,
        ));


        $_fieldset->addField('delay_retry', 'text', array(
            'name' => 'delay_retry',
            'label' => $this->__('Delay between retries, sec'),
            'title' => $this->__('Delay between retries, sec'),
            'class' => 'validate-digits',
        ));

        if (Mage::getSingleton('adminhtml/session')->getRandompriceData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getRandompriceData());
            Mage::getSingleton('adminhtml/session')->getRandompriceData(null);
        } elseif (Mage::registry('randomprice_data')) {
            $form->setValues(Mage::registry('randomprice_data')->getData());
        }

        return parent::_prepareForm();
    }

}