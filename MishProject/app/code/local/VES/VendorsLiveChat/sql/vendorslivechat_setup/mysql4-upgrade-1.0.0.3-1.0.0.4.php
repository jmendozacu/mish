<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $this->getTable('vendorslivechat/message'), //table name
    'increment_id',      //column name5
    "varchar(255) NULL default ''"  //datatype definition
);

$installer->endSetup();