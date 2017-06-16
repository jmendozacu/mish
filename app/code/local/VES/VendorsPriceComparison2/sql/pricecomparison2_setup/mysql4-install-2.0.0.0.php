<?php

$installer = $this;

$table = $installer->getConnection()
->newTable($installer->getTable('pricecomparison2/pricecomparison2'))
->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity'  => true,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
), 'Entity Id')
->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
), 'Parent Id')
->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
), 'Vendor Id')
->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
), 'Product Id')
->addColumn('condition', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
), 'Condition')
->addColumn('price', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
    'nullable'  => false,
), 'Price')
->addColumn('qty', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
    'nullable'  => false,
), 'Qty')
->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
    'nullable'  => false,
), 'Description')
->addColumn('additional_info', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
    'nullable'  => false,
), 'Description')
->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
    'unsigned'  => true,
    'nullable'  => false,
), 'Status')

;
$installer->getConnection()->createTable($table);

$installer->getConnection()->addForeignKey('FK_PRICE_COMPARISON_VENDOR_ID_VENDOR_VENDOR_ID', $installer->getTable('pricecomparison2/pricecomparison2'), 'vendor_id', $installer->getTable('vendors/vendor'), 'entity_id');
