<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $this->getTable('vendorslivechat/session'), //table name
    'customer_update_time',      //column name
    "varchar(255) NULL default '0'"  //datatype definition
);


$installer->getConnection()->addColumn(
    $this->getTable('vendorslivechat/session'), //table name
    'vendor_update_time',      //column name
    "varchar(255) NULL default '0'"  //datatype definition
);

$installer->endSetup();