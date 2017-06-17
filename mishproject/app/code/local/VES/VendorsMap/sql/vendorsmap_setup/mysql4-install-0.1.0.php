<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('vendorsmap/map')};
CREATE TABLE {$this->getTable('vendorsmap/map')} (
  `map_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `address` text NOT NULL default '',
  `telephone` varchar(255) NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `position` text NULL,
  `vendor_id` INT( 11 ) NOT NULL,
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 