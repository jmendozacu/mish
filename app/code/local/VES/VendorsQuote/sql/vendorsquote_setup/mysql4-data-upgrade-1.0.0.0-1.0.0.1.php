<?php 
$installer = $this;

$table = $installer->getConnection()
->newTable($installer->getTable('vendorsquote/message'))
->addColumn('message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity'  => true,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
), 'Quote Id')
->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned'  => true,
    'nullable'  => false,
), 'Quote Id')

->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable'  => false,
), 'Name')
->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
    'nullable'  => false,
), 'Message')
->addColumn('message_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
    'unsigned'  => true,
    'nullable'  => false,
), 'Is notify customer')
->addColumn('customer_notify', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
    'unsigned'  => true,
    'nullable'  => false,
), 'Is notify customer')
->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable'  => true,
), 'Created At')
;
$installer->getConnection()->createTable($table);
$installer->getConnection()->addForeignKey('FK_VENDOR_QUOTE_QUOTE_ID_MESSAGE_QUOTE_ID', $installer->getTable('vendorsquote/message'), 'quote_id', $installer->getTable('vendorsquote/quote'), 'quote_id');
$installer->endSetup();