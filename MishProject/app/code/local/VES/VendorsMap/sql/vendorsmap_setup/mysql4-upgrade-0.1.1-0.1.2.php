<?php
$installer = $this;
$installer->startSetup();


$installer->getConnection()->addColumn(
		$this->getTable('vendorsmap/map'),
		'region',
		'varchar(255) NULL'
);

$installer->getConnection()->addColumn(
    $this->getTable('vendorsmap/map'),
    'city',
    'varchar(255) NULL'
);

$installer->endSetup();
