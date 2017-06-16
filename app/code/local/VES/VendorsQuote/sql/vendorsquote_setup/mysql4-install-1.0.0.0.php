<?php

$installer = $this;
/**
 * Create table 'ves_vendor_quote'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('vendorsquote/quote'))
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Quote Id')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
        'nullable'  => false,
    ), 'Increment Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Store Id')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Vendor Id')
   	->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Customer Id')
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'First Name')
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Last Name')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Email')
    ->addColumn('company', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Company')
    ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
        'nullable'  => false,
    ), 'Telephone')
    ->addColumn('taxvat', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => false,
    ), 'Tax/VAT ID')
    ->addColumn('client_comment', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Client Comment')
    ->addColumn('internal_comment', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Internal Comment')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Status')
    ->addColumn('status_before_hold', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Status before hold')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => true,
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => true,
    ), 'Updated At')
    ->addColumn('expiry_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => true,
    ), 'Expiry date')
    ->addColumn('reminder_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => true,
    ), 'Reminder Date');
	$installer->getConnection()->createTable($table);


	/**
	 * Create table 'ves_vendor_quote_item'
	 */
	
	$table = $installer->getConnection()
	->newTable($installer->getTable('vendorsquote/item'))
	->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'identity'  => true,
	    'unsigned'  => true,
	    'nullable'  => false,
	    'primary'   => true,
	), 'Item Id')
	->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'unsigned'  => true,
	    'nullable'  => false,
	), 'Quote Id')
	->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'unsigned'  => true,
	    'nullable'  => false,
	), 'Product Id')
	->addColumn('parent_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'unsigned'  => true,
	    'nullable'  => false,
	), 'Parent Item Id')
	->addColumn('sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
	    'nullable'  => false,
	), 'SKU')
	->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
	    'nullable'  => false,
	), 'Name')
	->addColumn('price', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
	    'nullable'  => false,
	), 'price')
	->addColumn('client_comment', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
	    'nullable'  => false,
	), 'Client Comment')
	->addColumn('buy_request', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
	    'nullable'  => false,
	), 'Buy Request')
	->addColumn('default_proposal', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'nullable'  => false,
	), 'Default proposal')
	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
	    'nullable'  => true,
	), 'Created At')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
	    'nullable'  => true,
	), 'Updated At')

	;
	$installer->getConnection()->createTable($table);
	$installer->getConnection()->addForeignKey('FK_VENDOR_QUOTE_QUOTE_ID_ITEM_QUOTE_ID', $installer->getTable('vendorsquote/item'), 'quote_id', $installer->getTable('vendorsquote/quote'), 'quote_id');

	
	/**
	 * Create table 'ves_vendor_quote_item_proposal'
	 */
	$table = $installer->getConnection()
	->newTable($installer->getTable('vendorsquote/proposal'))
	->addColumn('proposal_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'identity'  => true,
	    'unsigned'  => true,
	    'nullable'  => false,
	    'primary'   => true,
	), 'Proposal Id')
	
	->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'unsigned'  => true,
	    'nullable'  => false,
	), 'Item Id')
	->addColumn('qty', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
	    'nullable'  => false,
	), 'QTY')
	->addColumn('price', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
	    'nullable'  => false,
	), 'Price')
	;
	
	$installer->getConnection()->createTable($table);
	$installer->getConnection()->addForeignKey('FK_VENDOR_QUOTE_ITEM_ITEM_ID_PROPOSAL_ITEM_ID', $installer->getTable('vendorsquote/proposal'), 'item_id', $installer->getTable('vendorsquote/item'), 'item_id');
	

	$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
	$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ves_enable_quotation', array(
	    'group' => 'General',
	    'sort_order' => 25,
	    'type' => 'int',
	    'backend' => '',
	    'frontend' => '',
	    'label' => 'Allow to Quote Mode',
	    'note' => '',
	    'input' => 'select',
	    'class' => '',
	    'source' => 'eav/entity_attribute_source_boolean',
	    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	    'visible' => true,
	    'required' => false,
	    'user_defined' => false,
	    'default' => '0',
	    'visible_on_front' => false,
	    'unique' => false,
	    'is_configurable' => false,
	    'used_for_promo_rules' => false,
	));
	$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ves_enable_order', array(
	    'group' => 'General',
	    'sort_order' => 26,
	    'type' => 'int',
	    'backend' => '',
	    'frontend' => '',
	    'label' => 'Allow to Order Mode',
	    'note' => '',
	    'input' => 'select',
	    'class' => '',
	    'source' => 'eav/entity_attribute_source_boolean',
	    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	    'visible' => true,
	    'required' => false,
	    'user_defined' => false,
	    'default' => '0',
	    'visible_on_front' => false,
	    'unique' => false,
	    'is_configurable' => false,
	    'used_for_promo_rules' => false,
	));
	$installer->endSetup(); 