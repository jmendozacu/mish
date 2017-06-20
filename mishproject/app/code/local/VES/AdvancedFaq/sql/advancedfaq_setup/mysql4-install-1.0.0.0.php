<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('advancedfaq/category')};
CREATE TABLE {$this->getTable('advancedfaq/category')} (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` varchar(255) DEFAULT '',
  `url_key` varchar(255) DEFAULT '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `updated_time` datetime NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('advancedfaq/faq')};
CREATE TABLE {$this->getTable('advancedfaq/faq')} (
		`faq_id` int(11) unsigned NOT NULL auto_increment,
		`question`  varchar(255) NOT NULL default '',
		`answer` text NOT NULL default '',
		`rating` float unsigned NOT NULL default 0,
		`votes` int unsigned NOT NULL default 0,
		`created_time` datetime NULL,
		`updated_time` datetime NULL,
		`status` smallint(6) NOT NULL default '0',
		`show_on` smallint(6) NOT NULL default '0',
		`sort_order` smallint(6) NOT NULL default '0',
		`category_id` int(11) NOT NULL default 0,
		PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 