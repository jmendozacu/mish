<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventory Adjust Stock Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Notificationlog_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('notificationlog_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('vendorsinventory')->__('Notification Log Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventorylowstock_Block_Adminhtml_Notificationlog_Edit_Tabs
     */
    protected function _beforeToHtml() {
        $this->addTab('products_section', array(
            'label' => Mage::helper('vendorsinventory')->__('Notification Log'),
            'title' => Mage::helper('vendorsinventory')->__('Notification Log'),
//                'content' => $this->getLayout()
//                    ->createBlock('inventorylowstock/adminhtml_notificationlog_edit_tab_products')
//                    ->toHtml(),
            'url' => $this->getUrl('*/*/product', array(
                '_current' => true,
                'id' => $this->getRequest()->getParam('id'),
                'store' => $this->getRequest()->getParam('store')
            )),
            'class' => 'ajax',
        ));
        return parent::_beforeToHtml();
    }

}
