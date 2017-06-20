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
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create inventory table
 */
$connection = $installer->getConnection();

if (Mage::helper('core')->isModuleEnabled('Magestore_Inventory')) {

    $installer->run("            
        CREATE TABLE IF NOT EXISTS  {$this->getTable('ves_vendor_warehouse_permission')}(
                `warehouse_permission_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `warehouse_id` int(11) unsigned NOT NULL,
                `vendor_id` int(11) unsigned NOT NULL,
                `can_edit_warehouse` tinyint(1) NOT NULL,
                `can_adjust` tinyint(1) NOT NULL,
                `can_purchase_product` tinyint(1) NOT NULL,
                `can_physical` tinyint(1) NOT NULL,
                `can_send_request_stock` tinyint(1) NOT NULL,
                INDEX (`warehouse_id`),
                INDEX (`vendor_id`),
                PRIMARY KEY(`warehouse_permission_id`),
                FOREIGN KEY (`warehouse_id`) REFERENCES {$this->getTable('erp_inventory_warehouse')}(`warehouse_id`) ON DELETE CASCADE ON UPDATE CASCADE
        )ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
} else {

    $installer->run("           
        DROP TABLE IF EXISTS {$this->getTable('ves_vendor_warehouse_permission')};		
        CREATE TABLE {$this->getTable('ves_vendor_warehouse_permission')}(
                `warehouse_permission_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `warehouse_id` int(11) unsigned NOT NULL,
                `vendor_id` int(11) unsigned NOT NULL,
                `can_edit_warehouse` tinyint(1) NOT NULL,
                `can_adjust` tinyint(1) NOT NULL,
                `can_purchase_product` tinyint(1) NOT NULL,
                `can_physical` tinyint(1) NOT NULL,
                `can_send_request_stock` tinyint(1) NOT NULL,
                INDEX (`warehouse_id`),
                INDEX (`vendor_id`),
                PRIMARY KEY(`warehouse_permission_id`),
                FOREIGN KEY (`warehouse_id`) REFERENCES {$this->getTable('erp_inventory_warehouse')}(`warehouse_id`) ON DELETE CASCADE ON UPDATE CASCADE
        )ENGINE=InnoDB DEFAULT CHARSET=utf8;        		
    ");
}

$installer->endSetup();

