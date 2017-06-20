<?php

$installer = $this;
$installer->startSetup();
$objCatalogEavSetup = new  VES_VendorsRma_Model_Resource_Setup();


$objCatalogEavSetup->addAttribute(VES_VendorsRma_Model_Request::ENTITY,'flag_state', array(
    'type'              => 'static',
    'label'             => 'Flag State',
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


$objCatalogEavSetup->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'flag_state', 'smallint(5) unsigned NOT NULL DEFAULT \'0\'');
$installer->endSetup();
