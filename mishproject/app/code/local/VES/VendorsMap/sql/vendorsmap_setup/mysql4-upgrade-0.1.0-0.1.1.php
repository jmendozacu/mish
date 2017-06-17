<?php
$installer = $this;
$installer->startSetup();


$installer->getConnection()->addColumn(
		$this->getTable('vendorsmap/map'),
		'logo',
		'text NULL'
);

$installer->endSetup();
