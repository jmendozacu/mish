<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('vendorssubaccount/account')};
CREATE TABLE {$this->getTable('vendorssubaccount/account')} (
  `account_id` int(11) unsigned NOT NULL auto_increment,
  `vendor_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `username` varchar(255) NOT NULL default '',
  `password_hash` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `telephone` varchar(255) NOT NULL default '',
  `rp_token` varchar(255) NOT NULL default '',
  `rp_token_created_at` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('vendorssubaccount/role')};
CREATE TABLE {$this->getTable('vendorssubaccount/role')} (
  `role_id` int(11) unsigned NOT NULL auto_increment,
  `vendor_id` int(11) unsigned NOT NULL,
  `role_name` varchar(255) NOT NULL default '',
  `resources` mediumtext NOT NULL default '',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 