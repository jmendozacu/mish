<?php

$installer = $this;

/*Create Category table*/
$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorscategory/category'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Category Id')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Vendor Id')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Is Active')
    ->addColumn('parent_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Parent Category Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Name')
    ->addColumn('url_key', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Url Key')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Description')
	->addColumn('meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Meta Title')
    ->addColumn('meta_keyword', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Meta keyword')
    ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_TEXT, Varien_Db_Ddl_Table::MAX_TEXT_SIZE, array(
        'nullable'  => false,
        ), 'Meta Description')
    ->addColumn('level', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        ), 'Level')
    ->addColumn('page_layout', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Page Layout')
    ->addColumn('is_hide_product', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
    	'unsigned'  => true,
        ), 'Is hide products')
        ;
$installer->getConnection()->createTable($table);
$installer->getConnection()->addForeignKey('FK_VENDOR_ID', $installer->getTable('vendorscategory/category'), 'vendor_id', $installer->getTable('vendors/vendor'), 'entity_id');
$installer->endSetup(); 