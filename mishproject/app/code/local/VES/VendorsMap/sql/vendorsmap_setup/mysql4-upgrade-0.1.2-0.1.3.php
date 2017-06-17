<?php
$installer = $this;
$installer->startSetup();


$installer->getConnection()->addColumn(
		$this->getTable('vendorsmap/map'),
		'country_id',
		'varchar(255) NULL'
);

$installer->getConnection()->addColumn(
    $this->getTable('vendorsmap/map'),
    'postcode',
    'varchar(255) NULL'
);

$installer->getConnection()->addColumn(
    $this->getTable('vendorsmap/map'),
    'region_id',
    'varchar(255) NULL'
);


$installer->endSetup();
