<?php

$installer = $this;

$installer->startSetup();

/**
 * create inventory table
 */
$connection = $installer->getConnection();

$installer->run("    
    ALTER TABLE {$this->getTable('inventoryplus/warehouse_history')} 
         ADD `vendor_id` int(11) NOT NULL;         
");

$installer->endSetup();
