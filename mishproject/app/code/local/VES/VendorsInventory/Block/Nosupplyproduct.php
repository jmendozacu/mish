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
 * Supplier Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Nosupplyproduct extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'nosupplyproduct';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('inventorypurchasing')->__('Non-Supplier Products');
        $this->_addButtonLabel = Mage::helper('inventorypurchasing')->__('Add Item');
        parent::__construct();
        $this->_removeButton('add');
    } 
    
}