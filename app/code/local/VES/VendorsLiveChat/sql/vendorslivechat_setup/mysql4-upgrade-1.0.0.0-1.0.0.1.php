<?php
$installer = $this;
$installer->startSetup();


$installer->getConnection()->addColumn(
    $this->getTable('vendorslivechat/session'), //table name
    'show_on_backend',      //column name
    "smallint(6) NOT NULL default '0'"  //datatype definition
);

$installer->getConnection()->addColumn(
    $this->getTable('vendorslivechat/session'), //table name
    'is_closed',      //column name
    "smallint(6) NOT NULL default '0'"  //datatype definition
);


$installer->endSetup();