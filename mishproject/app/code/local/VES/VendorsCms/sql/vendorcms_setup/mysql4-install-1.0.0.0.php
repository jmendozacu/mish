<?php
$installer = $this;
/**
 * Create table 'vendor_cms_page'
 */

	$installer->getConnection()->dropTable($installer->getTable('vendorscms/page'));
	$cmsPageTable = $installer->getConnection()
    ->newTable($installer->getTable('vendorscms/page'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Page Id')
   	->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Vendor Id')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Identifier')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Title')
    ->addColumn('content_heading', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Content Heading')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Content')
    ->addColumn('root_template', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Root Template')
    ->addColumn('meta_keywords', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Meta Keywords')
    ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Meta Description')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Update Time')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        ), 'Is Active')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        ), 'Sort Order')
    ->addForeignKey('FK_VENDOR_CMS_PAGE',
                    'vendor_id', $installer->getTable('vendors/vendor'), 'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
   	;
   $installer->getConnection()->createTable($cmsPageTable); 
/**
 * Create table 'vendor_cms_block'
 */
	$installer->getConnection()->dropTable($installer->getTable('vendorscms/block'));
	$cmsBlockTable = $installer->getConnection()
    ->newTable($installer->getTable('vendorscms/block'))
    ->addColumn('block_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Block Id')
   	->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Vendor Id')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Identifier')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Title')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Content')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Update Time')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        ), 'Is Active')
   	->addForeignKey('FK_VENDOR_CMS_BLOCK',
                    'vendor_id', $installer->getTable('vendors/vendor'), 'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
   	;
   	$installer->getConnection()->createTable($cmsBlockTable);
/**
 * Create table 'vendor_cms_app'
 */
	$installer->getConnection()->dropTable($installer->getTable('vendorscms/app'));
	$appBlockTable = $installer->getConnection()
    ->newTable($installer->getTable('vendorscms/app'))
    ->addColumn('app_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'App Id')
   	->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Vendor Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => false,
        ), 'App Type')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Title')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Sort Order')
   	->addForeignKey('FK_VENDOR_CMS_APP',
                    'vendor_id', $installer->getTable('vendors/vendor'), 'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
   	;
   	$installer->getConnection()->createTable($appBlockTable);
   	
/**
 * Create table 'vendor_cms_app_option'
 */   	
   	$installer->getConnection()->dropTable($installer->getTable('vendorscms/appoption'));
	$appBlockTable = $installer->getConnection()
    ->newTable($installer->getTable('vendorscms/appoption'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option Id')
   	->addColumn('app_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'App Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Option Code')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        ), 'Option Value')
   	;
   	$installer->getConnection()->createTable($appBlockTable);
	$installer->endSetup();