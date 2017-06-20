<?php
$installer = $this;
$installer->startSetup();


$installer->getConnection()->addColumn(
		$this->getTable('advancedfaq/faq'), //table name
		'seller_id',      //column name
		'INT( 11 ) NOT NULL'  //datatype definition
);

$installer->endSetup();
