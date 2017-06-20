<?php

$installer = $this;

$installer->startSetup();
//find more at Varien_Db_Adapter_Pdo_Mysql and Zend_Db_Adapter_Abstract classes.

if ($installer->getConnection()->isTableExists('geoipultimatelock')) {
    $installer->getConnection()
            ->dropTable('geoipultimatelock');
}

if ($installer->getConnection()->isTableExists('geoipblockedips')) {
    $installer->getConnection()
            ->dropTable('geoipblockedips');
}
$mainTable = $installer->getConnection()
        ->newTable('geoipultimatelock')
        ->addColumn('geoipultimatelock_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 255, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
                ), 'rule ID')
        ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'rule title')
        ->addColumn('notes', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
                ), 'notes for admin')
        ->addColumn('redirect_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'redirect url')
        ->addColumn('ips_exception', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
                ), 'exception for Ip ')
        ->addColumn('stores', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
                ), 'store seperated by , ')
        ->addColumn('cms_pages', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
                ), 'cms pages separated by ,')
        ->addColumn('rules', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'serialized input for rules')
        ->addColumn('blocked_countries', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'serialized input for blocked countries array')
        ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
            'nullable' => true,
                ), 'priority')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
                ), 'STATUS')
        ->addColumn('created_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
                ), 'Db created time')
        ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
        ), 'Db record updated time');

$installer->getConnection()->createTable($mainTable);
$installer->getConnection()->changeTableEngine(//method is used to change table engine
        'geoipultimatelock', //table name
        'InnoDB' //engine name
);

$blockedIpTable = $installer->getConnection()
        ->newTable('geoipblockedips')
        ->addColumn('geoipblockedips_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 255, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
                ), 'rule ID')
        ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 255, array(
            'nullable' => true,
                ), 'visitor id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 255, array(
            'nullable' => true,
                ), 'customer id')
        ->addColumn('blocked_ip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'blocked IP ')
        ->addColumn('remote_addr', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
                ), 'remote address ')
        ->addColumn('cms_pages', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
                ), 'cms pages separated by ,')
        ->addColumn('rules', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'serialized input for rules')
        ->addColumn('blocked_countries', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'serialized input for blocked countries array')
        ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
            'nullable' => true,
                ), 'priority')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'nullable' => true,
        ), 'STATUS');

$installer->getConnection()->createTable($blockedIpTable);
$installer->getConnection()->changeTableEngine(//method is used to change table engine
        'geoipblockedips', //table name
        'InnoDB' //engine name
);

$installer->setConfigData('geoipultimatelock/main/enable', '1');

$installer->endSetup();
