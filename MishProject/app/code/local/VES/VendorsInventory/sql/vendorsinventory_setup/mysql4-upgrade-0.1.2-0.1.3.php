<?php

$installer = $this;

$installer->startSetup();

/**
 * create inventory table
 */
$connection = $installer->getConnection();

$installer->run("    
    ALTER TABLE {$this->getTable('inventorypurchasing/supplier_product')} 
         ADD `vendor_id` int(11) NOT NULL;         
    ALTER TABLE {$this->getTable('inventorypurchasing/supplier_history')} 
         ADD `vendor_id` int(11) NOT NULL;         
    ALTER TABLE {$this->getTable('inventorypurchasing/supplier')} 
         ADD `vendor_id` int(11) NOT NULL;         
    ALTER TABLE {$this->getTable('inventorypurchasing/supplier_historycontent')} 
         ADD `vendor_id` int(11) NOT NULL;         
");

$installer->endSetup();
