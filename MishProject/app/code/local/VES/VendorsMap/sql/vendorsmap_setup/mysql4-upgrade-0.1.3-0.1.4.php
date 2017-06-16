<?php
$installer = $this;
$installer->startSetup();


$installer->getConnection()->addColumn(
		$this->getTable('vendorsmap/map'),
		'attribute',
		'varchar(255) NULL'
);

$installer->endSetup();
