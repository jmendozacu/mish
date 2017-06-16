<?php
$installer = $this;
$installer->startSetup();
$objCatalogEavSetup = new  VES_VendorsRma_Model_Resource_Setup();


$objCatalogEavSetup->addAttribute(VES_VendorsRma_Model_Request::ENTITY,'vendor_id', array(
    'type'              => 'static',
    'label'             => 'Vendor Id',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

/*
$objCatalogEavSetup->addAttribute(VES_VendorsRma_Model_Request::ENTITY,'address_id', array(
    'type'              => 'static',
    'label'             => 'Customer Id',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));
*/
$objCatalogEavSetup->addAttribute(VES_VendorsRma_Model_Request::ENTITY,'customer_id', array(
    'type'              => 'static',
    'label'             => 'Customer Id',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$objCatalogEavSetup->addAttribute(VES_VendorsRma_Model_Request::ENTITY,'customer_email', array(
    'type'              => 'static',
    'label'             => 'Customer Email',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$objCatalogEavSetup->addAttribute(VES_VendorsRma_Model_Request::ENTITY,'customer_name', array(
    'type'              => 'static',
    'label'             => 'Customer Name',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));


/* $objCatalogEavSetup->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'address_id', 'int(11) unsigned NOT NULL DEFAULT \'0\''); */

$objCatalogEavSetup->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'customer_id', 'int(11) unsigned NOT NULL DEFAULT \'0\'');
$objCatalogEavSetup->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'vendor_id', 'int(11) unsigned NOT NULL DEFAULT \'0\'');
$objCatalogEavSetup->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'customer_email', 'text DEFAULT NULL');
$objCatalogEavSetup->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'customer_name', 'varchar(600) DEFAULT NULL');
$installer->endSetup();
