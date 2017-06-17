<?php
$installer = $this;
$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('vendorsrma/address')};
    CREATE TABLE {$this->getTable('vendorsrma/address')} (
      `address_id` int(11) unsigned NOT NULL auto_increment,
      `lastname` varchar(255) NOT NULL default '',
      `firstname` varchar(255) NOT NULL default '',
      `company` text NOT NULL default '',
      `telephone` text NOT NULL default '',
      `fax` text NOT NULL default '',
      `address` text NOT NULL default '',
      `city` varchar(255) NOT NULL default '',
      `country_id` varchar(255) NOT NULL default '',
      `region` varchar(255) NOT NULL default '',
      `region_id` varchar(255) NOT NULL default '',
      `postcode` varchar(255) NOT NULL default '',
      `additional_information` text NOT NULL default '',
      `request_id` int(11) NOT NULL default '0',

      PRIMARY KEY (`address_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");


$installer->getConnection()->addConstraint('PK_adresss_request',$installer->getTable('vendorsrma/address'),'request_id',
    $installer->getTable('vendorsrma/request'),'entity_id',
    'cascade',
    'cascade'

);

$installer->endSetup();


