<?php

$installer = $this;

$installer->startSetup();

/**
 * create inventory table
 */
$connection = $installer->getConnection();

$installer->run("
    ALTER TABLE {$this->getTable('inventoryplus/warehouse')} 
         ADD `warehouse_created_by` enum('1','2') NOT NULL DEFAULT '1';
    ALTER TABLE {$this->getTable('inventoryplus/warehouse')} 
         ADD `vendor_id` int(11) NOT NULL;         
");

$installer->endSetup();
